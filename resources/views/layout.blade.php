<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>@yield('title') - Imageblast</title>
        <link rel="stylesheet" href="/css/app.css" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    </head>
    <body>
        <div id="app">
            <nav class="navbar is-transparent is-dark">
                <div class="container">
                    <div class="navbar-brand">
                        <a class="navbar-item" href="/">
                            <span>Imageblast</span>
                        </a>
                        <div class="navbar-burger burger" data-target="navbarFull">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>

                    <div id="navbarFull" class="navbar-menu">
                        <div class="navbar-end">
                            <div class="navbar-item">
                                <div class="field is-grouped">
                                    <p class="control">
                                        <a href="/login" class="button is-dark">Login</a>
                                    </p>
                                    <p class="control">
                                        <a href="/signup" class="button is-primary">Signup</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
            @yield('hero')
            
            <div class="container">
                @yield('content')
            </div>

        </div>
        <script src="/js/app.js"></script>
        @yield('scripts')
    </body>
</html>
