<x-jet-form-section submit="updateShippingAddress">
    <x-slot name="title">
        {{ __('Shipping Address') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s shipping address.') }}
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
            <select class="w-full p-3 border border-gray-200 rounded-3xl sm:text-sm" wire:model.defer="address.country_id">
                <option value>Select a country</option>
                @foreach ($this->countries as $country)
                <option value="{{ $country->id }}" wire:key="country_{{ $country->id }}">
                    {{ $country->native }}
                </option>
                @endforeach
            </select>
        </x-input.group>


        <x-input.group class="col-span-3 sm:col-span-2" :errors="$errors->get('address.postcode')">
            <label class="flex items-center p-2 rounded-lg cursor-pointer hover:bg-gray-50">
                <input
                    class="w-5 h-5 text-green-600 border-gray-100 rounded"
                    type="checkbox"
                    wire:model="shippingIsBilling"
                    wire:change="$emitTo('profile.update-billing-address-form', 'shippingIsBilling', $event.target.checked)"
                />

                <span class="ml-2 text-xs font-medium">
                    {{ __('Use for billing') }}
                </span>
            </label>
        </x-input.group>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            {{ __('Saved.') }}
        </x-jet-action-message>

        <x-jet-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-jet-button>
    </x-slot>
</x-jet-form-section>