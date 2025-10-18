<x-guest-layout>
    <jet-authentication-card>
        <x-slot name="logo">
        </x-slot>

        <jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-3">
            @csrf

            <x-input.group label="{{ __('Name') }}">
                <x-input.text id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </x-input.group>

            <x-input.group class="mt-4" label="{{ __('Email') }}">
                <x-input.text id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </x-input.group>

            <x-input.group class="mt-4" label="{{ __('Password') }}">
                <x-input.text id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </x-input.group>

            <x-input.group class="mt-4" label="{{ __('Confirm Password') }}">
                <x-input.text id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </x-input.group>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <jet-label for="terms">
                        <div class="flex items-center">
                            <jet-checkbox name="terms" id="terms"/>

                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </jet-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <jet-button class="ml-4">
                    {{ __('Register') }}
                </jet-button>
            </div>
        </form>
    </jet-authentication-card>
</x-guest-layout>
