

<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.title-meta')
    @include('partials.head-css')

     <!-- Token CSRF pour les requêtes AJAX -->
    <!--- Le token CSRF est requis pour les requêtes AJAX que nous utilisons pour les likes/dislikes des commentaires. Sans ce token, Laravel bloquera les requêtes POST par sécurité. --->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('css')
</head>
<body>
    @include('partials.header')
    @include('partials.sidebar')
    @include('partials.horizontal')

    <main class="app-wrapper d-flex flex-column min-vh-100">
        <div class="container-fluid flex-grow-1">
            @include('partials.page-title')
            @yield('content')
            @include('partials.switcher')
            @include('partials.scroll-to-top')
        </div>
        @include('partials.footer')
    </main>

    @include('partials.vendor-scripts')
    @yield('js')
</body>
</html>
