<x-jet-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('Update Password') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </x-slot>

    <x-slot name="form">
        <x-input.group class="col-span-6 sm:col-span-4" label="{{ __('Current Password') }}" :errors="$errors->get('current_password')">
            <x-input.text id="current_password" type="password" class="mt-1 block w-full" wire:model.defer="state.current_password" autocomplete="current-password" />
        </x-input.group>

        <x-input.group class="col-span-6 sm:col-span-4" label="{{ __('New Password') }}" :errors="$errors->get('password')">
            <x-input.text id="password" type="password" class="mt-1 block w-full" wire:model.defer="state.password" autocomplete="new-password" />
        </x-input.group>

        <x-input.group class="col-span-6 sm:col-span-4" label="{{ __('Confirm Password') }}" :errors="$errors->get('password_confirmation')">
            <x-input.text id="password_confirmation" type="password" class="mt-1 block w-full" wire:model.defer="state.password_confirmation" autocomplete="new-password" />
        </x-input.group>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button>
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section>
