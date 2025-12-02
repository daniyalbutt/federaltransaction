<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="vh-100">
    @yield('content')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const showPassIcons = document.querySelector('.show-pass');
            
            showPassIcons.addEventListener('click', function() {
                // Toggle the password input type
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                
                // Toggle the eye icons visibility
                const eyeSlash = this.querySelector('.fa-eye-slash');
                const eye = this.querySelector('.fa-eye');
                
                if (isPassword) {
                    eyeSlash.style.display = 'none';
                    eye.style.display = 'inline-block';
                } else {
                    eyeSlash.style.display = 'inline-block';
                    eye.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
