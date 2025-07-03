<!--
    "PRA QUE SERVE
    TANTO CÓDIGO
    SE A VIDA
    NÃO É PROGRAMADA
    E AS MELHORES COISAS
    NÃO TEM LÓGICA".
    Algúem (algum ano)
-->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <script>
        (function () {
            const savedMode = localStorage.getItem('dark-mode');
            const isDarkMode = savedMode === "true";
            if (isDarkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.add('light');
            }
        })();
    </script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/style.css?v=22') }}">
    <link rel="stylesheet" href="{{ asset('css/vars.css?v=22') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar-pc.css?v=21') }}">
     <script src="{{asset('lucide@0.511.0/dist/umd/lucide.min.js')}}"></script>
     <script src="https://unpkg.com/lucide@latest"></script>
     <link href="{{asset('bootstrap-5.0.0/cdn.jsdelivr.net_npm_bootstrap@5.0.2_dist_css_bootstrap.min.css')}}" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="{{asset('bootstrap-5.0.0/cdn.jsdelivr.net_npm_@popperjs_core@2.9.2_dist_umd_popper.min.js')}}" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="{{asset('bootstrap-5.0.0/cdn.jsdelivr.net_npm_bootstrap@5.0.2_dist_js_bootstrap.min.js')}}" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <meta name="description" content="@yield('meta_description', 'Cronos é um sistema de turmas e grupos privados!')">
    <meta name="keywords" content="@yield('meta_keywords', 'Cronos, rede social, fazer amigos, conexões online, comunidade digital, rede social brasileira, criar perfil online, compartilhar momentos, paçoca')">
    <meta name="author" content="Cronos Inc.">

    <meta property="og:title" content="@yield('title', 'Cronos - Entre em grupos e comunidades do seu interesse')">
    <meta property="og:description" content="@yield('meta_description', 'Cronos é um sistema de turmas e grupos privados!')">
    <meta property="og:image" content="@yield('meta_image', asset('img/estante_icon_fundo.png'))">
    <meta property="og:url" content="@yield('meta_url', url()->current())">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', 'Cronos - Entre em grupos e comunidades do seu interesse')">
    <meta name="twitter:description" content="@yield('meta_description', 'Cronos é um sistema de turmas e grupos privados!')">
    <meta name="twitter:image" content="@yield('meta_image', asset('img/estante_icon_fundo.png'))">

    {{-- <link href="{{asset('bootstrap-5.3.0-dist/css/bootstrap.min.css')}}" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="{{asset('bootstrap-5.3.0-dist/js/bootstrap.bundle.min.js')}}" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>    
    <link href="{{ asset('bootstrap-5.3.0-dist\css\bootstrap.min.css') }}" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous"> --}}
</head>

  @yield('content')

  <script>
      
    //   const savedMode = localStorage.getItem('dark-mode');
    //   const isDarkMode = savedMode == "true";
    //   localStorage.setItem('dark-mode', JSON.stringify(isDarkMode));
      
      
    //   // Adiciona ou remove a classe 'dark' no root
    // if (isDarkMode) {
    //     document.documentElement.classList.add('dark'); // Adiciona a classe 'dark'
    //     document.documentElement.classList.remove('light'); // Adiciona a classe 'dark'
    // } else {
    //     document.documentElement.classList.add('light'); // Remove a classe 'dark'
    //     document.documentElement.classList.remove('dark'); // Remove a classe 'dark'
    // }

    lucide.createIcons();

    console.log('%cDesenvolvido pelo João Enrique', 'font-size: 30px; color: red;');
    console.log(`%chttps://github.com/JoaoEnrique`, 'font-size: 20px; color: #5bb4ff;');
    console.log(`%Cronos ${new Date().getFullYear()}`, 'font-size: 30px; color: #5bb4ff;');
  </script>

</html>