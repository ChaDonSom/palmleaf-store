<?php

namespace App\Http\Livewire\Profile;

use GetCandy\Models\Address;
use GetCandy\Models\Country;
use GetCandy\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateBillingAddressForm extends Component
{
    public ?Address $address = null;

    public ?Customer $customer = null;

    public bool $editing = false;

    public $listeners = [
        'shippingIsBilling' => 'updateShippingIsBilling'
    ];

    public function render()
    {
        return view('livewire.profile.update-billing-address-form');
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
        $this->address = $this->customer->addresses()?->where('billing_default', true)?->first()
            ?? new Address([
                'customer_id' => $this->customer->id,
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
                'billing_default' => true,
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
            'address.shipping_default' => 'required|boolean|declined',
            'address.billing_default' => 'required|boolean|accepted',
        ];
    }

    public function updateBillingAddress()
    {
        $validatedData = $this->validate();

        clock()->info($validatedData['address']);

        $this->address->fill($validatedData['address'])->save();

        $this->emit('saved');
    }

    public $shippingIsBilling = true;

    public function updateShippingIsBilling($value) {
        $this->shippingIsBilling = $value;
    }
}
