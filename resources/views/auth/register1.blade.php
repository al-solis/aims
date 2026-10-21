<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Last Name -->
        <div>
            <x-input-label for="lname" class="text-xs" :value="__('Last Name')" />
            <x-text-input id="lname" class="block mt-1 w-full text-xs" type="text" name="lname" :value="old('lname')"
                required autofocus autocomplete="lname" />
            <x-input-error :messages="$errors->get('lname')" class="mt-2" />
        </div>

        <!-- First Name -->
        <div class="mt-2">
            <x-input-label for="fname" class="text-xs" :value="__('First Name')" />
            <x-text-input id="fname" class="block mt-1 w-full text-xs" type="text" name="fname"
                :value="old('fname')" required autofocus autocomplete="fname" />
            <x-input-error :messages="$errors->get('fname')" class="mt-2" />
        </div>
        <!-- Middle Name -->
        <div class="mt-2">
            <x-input-label for="mname" class="text-xs" :value="__('Middle Name')" />
            <x-text-input id="mname" class="block mt-1 w-full text-xs" type="text" name="mname"
                :value="old('mname')" autofocus autocomplete="mname" />
            <x-input-error :messages="$errors->get('mname')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-2">
            <x-input-label for="email" class="text-xs" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full text-xs" type="email" name="email"
                :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-2">
            <x-input-label for="password" class="text-xs" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full text-xs" type="password" name="password" required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-2">
            <x-input-label for="password_confirmation" class="text-xs" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full text-xs" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-2">
            <a class="underline text-xs text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4 text-xs">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
