<?php

namespace App\Http\Livewire\Profile;

use GetCandy\Models\Address;
use GetCandy\Models\Country;
use GetCandy\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateShippingAddressForm extends Component
{
    public ?Address $address = null;

    public ?Customer $customer = null;

    public bool $shippingIsBilling = true;

    public bool $editing = false;

    public function render()
    {
        return view('livewire.profile.update-shipping-address-form');
    }

    public function getCountriesProperty()
    {
        return Country::whereIn('iso3', ['GBR', 'USA'])->get();
    }

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        $this->customer = Auth::user()->customers->first();
        $this->address = $this->customer->addresses()?->where('shipping_default', true)?->first()
            ?? new Address([
                'customer_id' => $this->customer->id,
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
                'shipping_default' => true,
            ]);

        // If we have an existing ID then it should already be valid and ready to go.
        $this->editing = (bool) !$this->address->id;
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            'address.customer_id' => 'required',
            'address.first_name' => 'required',
            'address.last_name' => 'required',
            'address.line_one' => 'required',
            'address.country_id' => 'required',
            'address.city' => 'required',
            'address.postcode' => 'required',
            'address.company_name' => 'nullable',
            'address.line_two' => 'nullable',
            'address.line_three' => 'nullable',
            'address.state' => 'nullable',
            'address.delivery_instructions' => 'nullable',
            'address.contact_email' => 'nullable|email',
            'address.contact_phone' => 'nullable',
            'address.shipping_default' => 'required|boolean|accepted',
        ];
    }

    public function updateShippingAddress() {
        $validatedData = $this->validate();

        $this->address->fill($validatedData['address'])->save();

        if ($this->shippingIsBilling) {
            $billingAddress = $this->customer->addresses()?->where('billing_default', true)?->first()
                ?? $this->address->replicate();
            $billingAddress->fill(array_merge($validatedData['address'], [
                'shipping_default' => false,
                'billing_default' => true,
            ]))->save();
        }

        $this->emit('saved');
    }
}
