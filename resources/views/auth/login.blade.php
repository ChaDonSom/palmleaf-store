<x-app-layout>
    <x-authentication-card>
        <x-slot name="logo">
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-3">
            @csrf

            <x-input.group label="{{ __('Email') }}">
                <x-input.text id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </x-input.group>

            <x-input.group label="{{ __('Password') }}">
                <x-input.text id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </x-input.group>

            <x-input.group>
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </x-input.group>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
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
