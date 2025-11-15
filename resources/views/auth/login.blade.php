<x-app-layout>
    <x-authentication-card>
        <x-slot name="logo">
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 text-sm font-medium text-slate-900 dark:text-slate-200">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-3">
            @csrf

            <h3 class="dark:text-white">{{ __('Log in') }}</h3>

            <x-input.group>
                <x-input.text id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required autofocus placeholder="{{ __('Email') }}" />
            </x-input.group>

            <x-input.group>
                <x-input.text id="password" class="block w-full mt-1" type="password" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}" />
            </x-input.group>

            <x-input.group>
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                </label>
            </x-input.group>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="text-sm text-gray-600 dark:text-gray-400 underline hover:text-gray-900 dark:hover:text-gray-100" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ml-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-app-layout>
