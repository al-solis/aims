<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Hanken+Grotesk:wght@400;500;600&display=swap');

        :root {
            --ink: #0f2a2b;
            --ink-soft: #1c3d3e;
            --brass: #b08d57;
            --brass-dark: #8f6f3f;
            --line: #d9ded9;
        }

        /* Wallpaper with a deep green overlay */
        body {
            background-image:
                linear-gradient(rgba(15, 42, 43, .82), rgba(15, 42, 43, .88)),
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

        .login-card {
            background: #fff;
            border-top: 3px solid var(--brass);
            border-radius: 2px;
            padding: 2.5rem 2.25rem;
            box-shadow: 0 24px 60px -20px rgba(0, 0, 0, .55);
        }

        .login-card .login-title {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 2.25rem;
            font-weight: 600;
            line-height: 1.1;
            color: var(--ink);
        }

        /* Restyle the existing components without touching their markup */
        .login-card input[type="email"],
        .login-card input[type="password"] {
            border-color: var(--line);
            border-radius: 2px;
            padding: .7rem .9rem;
            box-shadow: none;
            transition: border-color .15s, box-shadow .15s;
        }

        .login-card input[type="email"]:focus,
        .login-card input[type="password"]:focus {
            border-color: var(--ink);
            box-shadow: 0 0 0 3px rgba(176, 141, 87, .25);
            outline: none;
        }

        .login-card input[type="checkbox"] {
            border-radius: 2px;
            color: var(--ink);
        }

        .login-card input[type="checkbox"]:focus {
            --tw-ring-color: var(--brass);
        }

        .login-card button[type="submit"] {
            background: var(--ink);
            border-radius: 2px;
            padding: .7rem 1.4rem;
            letter-spacing: .02em;
            text-transform: none;
            font-weight: 600;
            transition: background .15s;
        }

        .login-card button[type="submit"]:hover,
        .login-card button[type="submit"]:focus {
            background: var(--ink-soft);
        }

        .login-card button[type="submit"]:focus-visible {
            outline: 2px solid var(--brass);
            outline-offset: 2px;
        }

        .login-card a {
            color: var(--ink);
            text-decoration-color: var(--brass);
            text-underline-offset: 3px;
        }

        .login-card a:hover {
            color: var(--brass-dark);
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                transition: none !important;
            }
        }
    </style>

    <div class="login-card">
        <h1 class="login-title mb-6">{{ __('Sign in') }}</h1>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" class="text-xs" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full text-xs" type="email" name="email"
                    :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" class="text-xs" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full text-xs" type="password" name="password" required
                    autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                        name="remember">
                    <span class="ms-2 text-xs text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-6">
                {{-- @if (Route::has('password.request')) --}}
                @if (route('password.request', absolute: false))
                    <a class="underline text-xs text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button class="ms-3 text-xs">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
