<?php

namespace Tests\Unit\Http\Livewire\Profile;

use App\Livewire\Profile\UpdateAddressForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Lunar\Models\Address;
use Lunar\Models\Country;
use Lunar\Models\Customer;
use Tests\TestCase;

class UpdateAddressFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test that the component dispatches 'saved' event after updating address.
     *
     * @return void
     */
    public function test_dispatches_saved_event_on_update()
    {
        $user = \App\Models\User::factory()->create();
        $customer = Customer::factory()->create();
        $customer->users()->attach($user);

        $country = Country::factory()->create([
            'iso3' => 'USA',
        ]);

        $address = Address::factory()->create([
            'customer_id' => $customer->id,
            'country_id' => $country->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'line_one' => '123 Test Street',
            'city' => 'Test City',
            'postcode' => '12345',
            'contact_email' => 'test@example.com',
            'shipping_default' => false,
            'billing_default' => false,
        ]);

        $this->actingAs($user);

        Livewire::test(UpdateAddressForm::class, ['address' => $address])
            ->set('address.line_one', '456 Updated Street')
            ->call('updateAddress')
            ->assertDispatched('saved');
    }

    /**
     * Test that the component dispatches 'saved' event after deleting address.
     *
     * @return void
     */
    public function test_dispatches_saved_event_on_delete()
    {
        $user = \App\Models\User::factory()->create();
        $customer = Customer::factory()->create();
        $customer->users()->attach($user);

        $country = Country::factory()->create([
            'iso3' => 'USA',
        ]);

        $address = Address::factory()->create([
            'customer_id' => $customer->id,
            'country_id' => $country->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'line_one' => '123 Test Street',
            'city' => 'Test City',
            'postcode' => '12345',
            'contact_email' => 'test@example.com',
            'shipping_default' => false,
            'billing_default' => false,
        ]);

        $this->actingAs($user);

        Livewire::test(UpdateAddressForm::class, ['address' => $address])
            ->call('deleteAddress')
            ->assertDispatched('saved');
    }
}
