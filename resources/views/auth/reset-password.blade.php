<x-guest-layout>
    <jet-authentication-card>
        <x-slot name="logo">
            <jet-authentication-card-logo />
        </x-slot>

        <jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="block">
                <jet-label for="email" value="{{ __('Email') }}" />
                <jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus />
            </div>

            <div class="mt-4">
                <jet-label for="password" value="{{ __('Password') }}" />
                <jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <jet-button>
                    {{ __('Reset Password') }}
                </jet-button>
            </div>
        </form>
    </jet-authentication-card>
</x-guest-layout>
