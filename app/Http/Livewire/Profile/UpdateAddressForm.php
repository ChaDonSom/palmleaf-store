<?php

namespace App\Http\Livewire\Profile;

use GetCandy\Models\Address;
use GetCandy\Models\Country;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateAddressForm extends Component
{
    public function render()
    {
        return view('livewire.profile.update-address-form');
    }

    public ?Address $address = null;

    public function mount() {
        $user = Auth::user();
        if (!$this->address) $this->address = new Address([
            'customer_id' => $user->customers->first()->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'contact_email' => $user->email,
            'shipping_default' => false,
            'billing_default' => false,
        ]);
    }

    public function getCountriesProperty()
    {
        return Country::whereIn('iso3', ['GBR', 'USA'])->get();
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
            'address.shipping_default' => 'required|boolean',
            'address.billing_default' => 'required|boolean',
        ];
    }

    public function updateAddress()
    {
        $validatedData = $this->validate();

        if ($this->address->billing_default) {
            $this->address->customer->addresses()->where('id', '!=', $this->address->id)
                ->where('billing_default', true)->update([
                    'billing_default' => false,
                ]);
        }

        if ($this->address->shipping_default) {
            $this->address->customer->addresses()->where('id', '!=', $this->address->id)
                ->where('shipping_default', true)->update([
                    'shipping_default' => false,
                ]);
        }

        $this->emit('saved');
    }
}
