<x-guest-layout>
    <link rel="stylesheet" href="{{ asset('assets/css/form-theme.css') }}">

    {{-- Colors come from <html data-theme="..."> in the guest layout; do not set data-theme here --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Hanken+Grotesk:wght@400;500;600&display=swap');

        /* Wallpaper with an overlay tinted by the theme's ink color */
        body {
            background-image:
                linear-gradient(color-mix(in srgb, var(--ft-ink) 82%, transparent),
                    color-mix(in srgb, var(--ft-ink) 88%, transparent)),
                url('{{ asset('images/wallpaper.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Hanken Grotesk', system-ui, sans-serif;
        }

        /* Strip the default guest-layout card so .login-card is the only surface */
        div:has(> .login-card) {
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
            max-width: 26rem !important;
            overflow: visible !important;
        }

        /* Card border: a soft line in the theme's accent, visible against the wallpaper */
        .login-card {
            border: 1px solid color-mix(in srgb, var(--ft-accent) 60%, transparent);
        }

        .login-card .ft-card-head h1 {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 2rem;
            line-height: 1.1;
        }

        /* Remember-me checkbox and forgot-password link follow the theme */
        .login-card input[type="checkbox"] {
            border-radius: 3px;
            border-color: var(--ft-border);
            color: var(--ft-ink);
        }

        .login-card input[type="checkbox"]:checked {
            border-color: var(--ft-ink);
        }

        .login-card input[type="checkbox"]:focus {
            --tw-ring-color: var(--ft-accent);
        }

        .login-card a {
            color: var(--ft-ink);
            text-decoration-color: var(--ft-accent);
            text-underline-offset: 3px;
        }

        .login-card a:hover {
            color: var(--ft-accent-dark);
        }

        .login-card a:focus {
            --tw-ring-color: var(--ft-accent);
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                transition: none !important;
            }
        }
    </style>

    <div class="ft-card login-card">
        <div class="ft-card-head">
            <h1>{{ __('Sign in') }}</h1>
            <p>{{ __('Enter your email and password to continue.') }}</p>
        </div>

        <div class="ft-body">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="space-y-4">
                    <!-- Email Address -->
                    <div>
                        <label for="email" class="ft-label">{{ __('Email') }}</label>
                        <input id="email" class="ft-field" type="email" name="email" value="{{ old('email') }}"
                            required autofocus autocomplete="username">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="ft-label">{{ __('Password') }}</label>
                        <input id="password" class="ft-field" type="password" name="password" required
                            autocomplete="current-password">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                name="remember">
                            <span class="ms-2 text-xs text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                        </label>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="ft-divider mt-6 pt-5 flex items-center justify-between gap-x-2">
                    {{-- @if (Route::has('password.request')) --}}
                    @if (route('password.request', absolute: false))
                        <a class="underline text-xs rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                            href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <button type="submit" class="ft-btn ft-btn-primary ms-auto">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
