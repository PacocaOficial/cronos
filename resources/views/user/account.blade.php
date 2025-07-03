{{-- 
    "PRA QUE SERVE
    TANTO CÓDIGO
    SE A VIDA
    NÃO É PROGRAMADA
    E AS MELHORES COISAS
    NÃO TEM LÓGICA". 
    Algúem (algum ano)
--}}

@extends('layouts.main')
@section('title', 'Cronos')

{{-- Conteudo do site --}}
@section('content')
    <body class="body-main">
        @include('layouts/menu')
        <div class="container container-account" style="padding-bottom: 100px; position: relative">
            {{-- MENSAGEM DE SUCESSO --}}
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 30px!important;">
                    {{session()->get('success')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- MENSAGEM DE ERRO --}}
            @if(session()->has('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 30px!important;">
                    {{session()->get('danger')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="row" style="justify-content: center;">
                <div class="col-md-8 col-sm-8 col-name-usser">
                    <h2>{{$user->name}}</h2>
                    <p>{{"@$user->user_name"}}</p>

                    @if(auth()->check() && $user->id == auth()->user()->id)
                        {{-- <a target="_blank" href="{{config("app.pacoca_url")}}/conta" class="btn btn-yellow" style="width: 100%;">
                            Editar
                        </a> --}}
                    @else
                    
                        @php
                            $admin_controller = app(App\Http\Controllers\AdminController::class);
                            $isAdmin = auth()->check() && $admin_controller->isAdmin(auth()->user()->id);
                        @endphp


                        <div class="d-flex justify-content-between">
                            <a target="_blank" href="{{config("app.pacoca_url")}}/{{"@" . $user->user_name}}" class="btn btn-yellow" style="width: {{ $user->readbook_user_id ? "50%" : "100%"}};">
                                Paçoca
                            </a>

                            @if($user->readbook_user_id )
                                <a target="_blank" href="{{config("app.readbooks_url")}}/compartilhar-livro/{{$user->id}}" class="ml-3 btn btn-yellow" style="width: calc(50% - 5px);">
                                    Read Books
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                @php 
                    $path = config("app.pacoca_api_url") . "/" . auth()->user()->img_account;

                    if (file_exists($path)) {
                        $img_account = config("app.pacoca_api_url") . "/" .  $path;
                    } else {
                        $img_account = asset('img/img_account/img_account.png');
                    }
                @endphp


                <div class="col-md-2 col-sm-2 col-img-cosnta">
                    <div class="img-conta-perfil" style="background-image: url('{{$img_account}}')"></div>
                </div>
            </div>
    
            <div class="row">
                <div class="hr"></div>
            </div>

        @if(auth()->check() && $user->id == auth()->user()->id)
            <div class="row" style="justify-content: center; margin-top: 50px">
                <div class="col-10">
                    <p>Atualize as informações da sua conta pelo <a href="{{config("app.pacoca_url")}}/conta" target="_blank" rel="noopener noreferrer">Central de Contas</a> Alterações são refletidas no Paçoca, Read Books, Cronos e Rita!    </p>
                </div>
            @endif
        </div>
    </body>
@endsection

