<x-guest-layout>
    <jet-authentication-card>
        <x-slot name="logo">
            <jet-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <jet-label for="password" value="{{ __('Password') }}" />
                <jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" autofocus />
            </div>

            <div class="flex justify-end mt-4">
                <jet-button class="ml-4">
                    {{ __('Confirm') }}
                </jet-button>
            </div>
        </form>
    </jet-authentication-card>
</x-guest-layout>
