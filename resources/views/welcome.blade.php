<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @if (config('app.theme')) data-theme="{{ config('app.theme') }}" @endif>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Guardforce Utilization, Asset & Resource Deployment System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="stylesheet" href="{{ asset('assets/css/form-theme.css') }}">
</head>

{{-- Wallpaper with an overlay tinted by the theme's ink color --}}

<body class="min-h-screen flex flex-col items-center justify-center p-6"
    style="background-image: linear-gradient(color-mix(in srgb, var(--ft-ink) 82%, transparent), color-mix(in srgb, var(--ft-ink) 90%, transparent)), url('{{ asset('images/wallpaper.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;">

    <main class="ft-card w-full max-w-md text-center"
        style="border: 1px solid color-mix(in srgb, var(--ft-accent) 60%, transparent);">

        {{-- Logo sits on the dark header band, as it did on the old dark panel --}}
        <div class="ft-card-head flex items-center justify-center" style="padding: 2rem;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-auto max-w-[150px] max-h-32">
        </div>

        <div class="ft-body" style="padding: 2rem 2rem 2.25rem;">
            <h1 class="text-3xl font-semibold leading-tight"
                style="color: var(--ft-ink); font-family: 'Cormorant Garamond', Georgia, serif;">
                {{ config('app.name', 'Guardforce Utilization, Asset & Resource Deployment System') }}
            </h1>

            <p class="mt-2 text-sm" style="color: var(--ft-muted);">
                @auth
                    Welcome back. Continue to your dashboard.
                @else
                    Sign in to continue.
                @endauth
            </p>

            @if (route('login', absolute: false))
                <nav class="ft-divider mt-6 pt-6 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    @auth
                        <a class="ft-btn ft-btn-primary justify-center sm:min-w-[9rem]" href="{{ url('/dashboard') }}">
                            Dashboard
                        </a>
                    @else
                        <a class="ft-btn ft-btn-primary justify-center sm:min-w-[9rem]" href="{{ route('login') }}">
                            Login
                        </a>

                        @if (route(name: 'register', absolute: false))
                            <a class="ft-btn ft-btn-ghost justify-center sm:min-w-[9rem]" href="{{ route('register') }}">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </main>

    <footer class="mt-6 text-xs" style="color: var(--ft-on-ink-muted);">
        &copy; {{ date('Y') }} {{ config('app.name', 'Asset and Workforce Management System') }}
    </footer>
</body>

</html>
