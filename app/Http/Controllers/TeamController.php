<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\UserTeam;
use App\Models\MessageTeam;
use App\Models\FileMessage;
use App\Models\User;
use App\Models\Hospedagem;
use Illuminate\Support\Facades\DB;
use Gate;

class TeamController extends Controller
{
    public $hospedagemModel;
    public $isHospedagem;

    public function __construct()
    {
        // Atribua os valores das propriedades no construtor da classe.
        $this->hospedagemModel = new Hospedagem();
        $this->isHospedagem = $this->hospedagemModel->isHospedagem();
    }

    public function index(){
       // 1. Buscar os registros de teams e users_teams (no banco padrão)
        $teams = DB::table('teams')
            ->join('users_teams', 'teams.id_teams', '=', 'users_teams.id_team')
            ->where('users_teams.id_user', auth()->id())
            ->select(
                'users_teams.*',
                'teams.*'
            )
            ->limit(20)
            ->get();

        // 2. Obter os IDs dos usuários encontrados
        $userIds = $teams->pluck('id_user')->unique()->toArray();

        // 3. Buscar os usuários no banco "pacoca"
        $users = DB::connection('pacoca')->table('users')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id'); // indexa por ID para facilitar uso

        // 4. Juntar os dados manualmente
        $teams = $teams->map(function ($team) use ($users) {
            $user = $users[$team->id_user] ?? null;

            return (object) array_merge((array) $team, [
                'user_name' => $user->user_name ?? null,
                'name_name' => $user->name ?? null,
            ]);
        });



        return view('user.teams', compact('teams'));
    }
    
    public function search(Request $request){
        $term = $request->term;
       // 1. Buscar os registros de teams e users_teams (no banco padrão)
        $teamsQuery = DB::table('teams')
            ->join('users_teams', 'teams.id_teams', '=', 'users_teams.id_team')
            ->where('users_teams.id_user', auth()->id())
            ->limit(10)
            ->select(
                'users_teams.*',
                'teams.*'
            );

        if($term){
            $teamsQuery->where(function ($query) use ($term) {
                $query->where("description", "LIKE", "%" .  $term . "%")
                    ->orWhere("name", "LIKE", "%" .  $term . "%")
                    ->orWhere("team_code", "LIKE", "%" .  $term . "%");
            });
        }

        $teams = $teamsQuery->get(); // AGORA é uma Collection

        // 2. Obter os IDs dos usuários encontrados
        $userIds = $teams->pluck('id_user')->unique()->toArray();

        // 3. Buscar os usuários no banco "pacoca"
        $users = DB::connection('pacoca')->table('users')
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');

        $teams = $teams->map(function ($team) use ($users) {
            $user = $users[$team->id_user] ?? null;

            return (object) array_merge((array) $team, [
                'user_name' => $user->user_name ?? null,
                'name_name' => $user->name ?? null,
            ]);
        });


        return view('user.teams', compact('teams', "term"));
    }

    public function new(){
        return view('admin.create_team');
    }

    public function store(Request $request){
        $utilsController = new UtilsController();

        // validar dados
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $team_code = $utilsController->generateRandomCode(6);// Gera código aleatorio com 6 digitos (A-Z - 0-9)
        $team = Team::where('team_code', $team_code)->get()->count();

        while($team){
            $team_code = $utilsController->generateRandomCode(6);// Gera código aleatorio com 6 digitos (A-Z - 0-9)
        }

        $team = Team::create([
            'id_user' => auth()->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'team_code' => $team_code,
            'closed' => !!$request->closed,
            'color' => $request->color,
        ]);

        UserTeam::create([
            'id_user' => auth()->user()->id,
            'id_team' => $team->id_teams,
        ]);

        return redirect("/teams/$team->team_code")->with('success', 'Turma criada');
    }

    function edit($id_team){
        $team = Team::where('id_teams', $id_team)->get()->first();

        if(!$team) return view('errors.404');
        if(Gate::denies('update-team', [$team])) return redirect("/teams/$team->team_code")->with('danger', 'Você não tem permissão para atualizar essa turma');

        return view('admin.edit_teams', ['team' => $team]);
    }

    public function update(Request $request){
        // validar dados
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $team = Team::where('id_teams', $request->id_team)->get()->first();
        if(!$team) return back()->with('danger', 'Turma não encontrada');
        if(Gate::denies('update-team', [$team])) return back()->with('danger', 'Você não tem permissão para atualizar essa turma');

        $team->update([
            'id_user' => auth()->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'closed' => !!$request->closed,
            'color' => $request->color,
        ]);

        return redirect("/teams/$team->team_code")->with('success', 'Turma atualizada');
    }

    public function delete($id_team){
        $team = Team::where('id_teams', $id_team)->get()->first();
        $messages = MessageTeam::where('id_team', $id_team)->get()->all();
        $users_team = UserTeam::where('id_team', $id_team)->get()->all();

        if(!$team) return back()->with('danger', 'Turma não encontrada');
        if(Gate::denies('delete-team', [$team])) return redirect("/teams/$team->team_code")->with('danger', 'Você não tem permissão para apagar essa turma');
        
        foreach($users_team as $user_team){
            $user_team->delete();
        }

        foreach($messages as $message){
            $files = FileMessage::where('id_message_team', $message->id_message_team)->get()->all();

            foreach($files as $file){
                $file->delete();
                $path_file = public_path($file->path_file); //pega caminho da imagem localhost (public/img/img_account)

                //verifica se tem imagem
                if (file_exists($path_file)) {
                    unlink($path_file); // Remove o arquivo
                }

            }

            $message->delete();

        }

        $team->delete();

        return redirect()->route('teams')->with('success', 'Turma excluida');
    }

    public function removeUser(Request $request, $id){
        $team = Team::where('id_teams', $request->id_team)->get()->first();
        if(!$team) return back()->with('danger', 'Turma não encontrada');

        $user = User::find($request->id_user);
        if(!$user)return back()->with('danger', 'Turma não encontrada');
        
        if(Gate::denies('remover-user', [$team, $user]))
            return back()->with('danger', 'Você não tem permissão para remover este usuário');

        UserTeam::where('id_user', $request->id_user)->where('id_team', $request->id_team)->get()->first()->delete();

        return back()->with('success', 'O usuário foi removido da turma');
    }

    public function enter(Request $request){
        try{
            $request->validate([
                'team_code' => ['required']
            ]);

            $team = Team::where('team_code', $request->team_code)->get()->first();


            // Código da turma invalido
            if(!$team){
                return back()->withErrors(['team_code' => 'O código da turma não é valido'])->withInput();
            }

            $user_team = UserTeam::where('id_user', auth()->user()->id)->where('id_team', $team->id_teams)->get()->first();

            if($user_team){
                return redirect()->intended(route('teams'))->with('warning', 'Você já está nessa turma');
            }

            $user_team = UserTeam::create([
                'id_user' => auth()->user()->id,
                'id_team' => $team->id_teams,
            ]);

            return redirect()->intended(route('teams'))->with('success', 'Você entrou na turma');
        }catch(\Exception $e){
            return redirect()->intended(route('teams'))->with('danger', $e->getMessage());
        }
    }

    public function view($team_code){
        $team = Team::where('team_code', $team_code)->get()->first();

        // 404
        if(!$team){
            return view('errors.404_team');
        }

        // 3. Junta as informações de usuários com as mensagens
        $messages = [];
        
        if(Gate::allows('view-messages', [$team])){
            // 1. Consulta as mensagens normalmente (sem tentar fazer JOIN com outro banco)
            $messages = DB::table('messages_team')
                ->where('id_team', $team->id_teams)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            // 2. Pega os usuários da conexão 'pacoca'
            $userIds = $messages->pluck('id_user')->unique()->toArray();

            $users = DB::connection('pacoca')
                ->table('users')
                ->whereIn('id', $userIds)
                ->select('id', 'name', 'user_name', 'img_account')
                ->get()
                ->keyBy('id');
                
            $messages = $messages->map(function ($message) use ($users) {
                $user = $users[$message->id_user] ?? null;

                return (object) array_merge((array) $message, [
                    'user' => $user, // Aqui colocamos o usuário completo como um objeto
                ]);
            });
        }

        return view('user.team', ['team' => $team, 'messages' => $messages]);
    }
}
