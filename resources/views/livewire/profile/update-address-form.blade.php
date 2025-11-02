<x-form-section submit="updateAddress" x-init="$wire.on('saved', () => history.back())">
    <x-slot name="title">
        {{ __('Edit Address') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update one of your account\'s addresses.') }}
    </x-slot>

    <x-slot name="form">
        <x-input.group class="col-span-3 sm:col-span-2" label="Address line 1" :errors="$errors->get('address.line_one')" required>
            <x-input.text wire:model.defer="address.line_one" required />
        </x-input.group>

        <x-input.group class="col-span-3 sm:col-span-2" label="Address line 2" :errors="$errors->get('address.line_two')">
            <x-input.text wire:model.defer="address.line_two" />
        </x-input.group>

        <x-input.group class="col-span-3 sm:col-span-2" label="Address line 3" :errors="$errors->get('address.line_three')">
            <x-input.text wire:model.defer="address.line_three" />
        </x-input.group>

        <x-input.group class="col-span-3 sm:col-span-2" label="City" :errors="$errors->get('address.city')" required>
            <x-input.text wire:model.defer="address.city" required />
        </x-input.group>

        <x-input.group class="col-span-3 sm:col-span-2" label="State / Province" :errors="$errors->get('address.state')">
            <x-input.text wire:model.defer="address.state" />
        </x-input.group>

        <x-input.group class="col-span-3 sm:col-span-2" label="Postcode" :errors="$errors->get('address.postcode')" required>
            <x-input.text wire:model.defer="address.postcode" required />
        </x-input.group>

        <x-input.group class="col-span-6" label="Country" required>
            <select class="w-full p-3 border border-gray-200 rounded-3xl sm:text-sm focus:border-green-600 focus:ring-green-600" wire:model.defer="address.country_id">
                <option value>Select a country</option>
                @foreach ($this->countries as $country)
                <option value="{{ $country->id }}" wire:key="country_{{ $country->id }}">
                    {{ $country->native }}
                </option>
                @endforeach
            </select>
        </x-input.group>

        <x-input.group class="col-span-3 sm:col-span-2" :errors="$errors->get('address.shipping_default')">
            <label class="flex items-center p-2 rounded-3xl cursor-pointer hover:bg-gray-50">
                <input
                    class="w-5 h-5 text-green-700 border-gray-100 rounded-3xl focus:ring-green-700"
                    type="checkbox"
                    wire:model.defer="address.shipping_default"
                />

                <span class="ml-2 text-xs font-medium">
                    {{ __('Use as default shipping address') }}
                </span>
            </label>
        </x-input.group>

        <x-input.group class="col-span-3 sm:col-span-2" :errors="$errors->get('address.billing_default')">
            <label class="flex items-center p-2 rounded-3xl cursor-pointer hover:bg-gray-50">
                <input
                    class="w-5 h-5 text-green-700 border-gray-100 rounded-3xl focus:ring-green-700"
                    type="checkbox"
                    wire:model.defer="address.billing_default"
                />

                <span class="ml-2 text-xs font-medium">
                    {{ __('Use as default billing address') }}
                </span>
            </label>
        </x-input.group>
    </x-slot>

    <x-slot name="actions">
        @if($address->id)
            <x-danger-button wire:loading.attr="disabled" type="button" class="mr-auto" wire:click="deleteAddress">
                {{ __('DELETE') }}
            </x-danger-button>
        @endif

        <x-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>