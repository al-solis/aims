<x-guest-layout>
    <link rel="stylesheet" href="{{ asset('assets/css/form-theme.css') }}">

    {{-- Colors come from <html data-theme="..."> in the guest layout; do not set data-theme here --}}
    <div class="ft-card ft-login mb-8">
        <div class="ft-card-head">
            <h1>{{ __('Create account') }}</h1>
            <p>{{ __('Fill in your details to register.') }}</p>
        </div>

        <div class="ft-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="space-y-1">
                    <!-- Last Name -->
                    <div>
                        <label for="lname" class="ft-label">{{ __('Last Name') }}</label>
                        <input id="lname" class="ft-field" type="text" name="lname" value="{{ old('lname') }}"
                            required autofocus autocomplete="lname">
                        <x-input-error :messages="$errors->get('lname')" class="mt-2" />
                    </div>

                    <!-- First Name -->
                    <div>
                        <label for="fname" class="ft-label">{{ __('First Name') }}</label>
                        <input id="fname" class="ft-field" type="text" name="fname" value="{{ old('fname') }}"
                            required autocomplete="fname">
                        <x-input-error :messages="$errors->get('fname')" class="mt-2" />
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label for="mname" class="ft-label">{{ __('Middle Name') }}</label>
                        <input id="mname" class="ft-field" type="text" name="mname" value="{{ old('mname') }}"
                            autocomplete="mname">
                        <x-input-error :messages="$errors->get('mname')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="ft-label">{{ __('Email') }}</label>
                        <input id="email" class="ft-field" type="email" name="email"
                            value="{{ old('email') }}" required autocomplete="username">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="ft-label">{{ __('Password') }}</label>
                        <input id="password" class="ft-field" type="password" name="password" required
                            autocomplete="new-password">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="ft-label">{{ __('Confirm Password') }}</label>
                        <input id="password_confirmation" class="ft-field" type="password" name="password_confirmation"
                            required autocomplete="new-password">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                {{-- Actions --}}
                <div class="ft-divider mt-6 pt-5 flex items-center justify-between gap-x-2">
                    <a class="underline text-xs rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2"
                        href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>

                    <button type="submit" class="ft-btn ft-btn-primary ms-auto">
                        {{ __('Register') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
