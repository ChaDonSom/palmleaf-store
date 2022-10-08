<?php

namespace App\Http\Livewire;

use App\Actions\Fortify\CreateNewUser;
use GetCandy\Facades\CartSession;
use GetCandy\Facades\Payments;
use GetCandy\Facades\ShippingManifest;
use GetCandy\Models\Cart;
use GetCandy\Models\CartAddress;
use GetCandy\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Rules\Password;
use Livewire\Component;
use Livewire\ComponentConcerns\PerformsRedirects;

class CheckoutPage extends Component
{
    use PerformsRedirects;

    /**
     * The Cart instance.
     *
     * @var Cart|null
     */
    public ?Cart $cart;

    /**
     * The shipping address instance.
     *
     * @var CartAddress|null
     */
    public ?CartAddress $shipping = null;

    /**
     * The billing address instance.
     *
     * @var CartAddress|null
     */
    public ?CartAddress $billing = null;

    /**
     * The current checkout step.
     *
     * @var int
     */
    public int $currentStep = 1;

    /**
     * Whether the shipping address is the billing address too.
     *
     * @var bool
     */
    public bool $shippingIsBilling = true;

    /**
     * The chosen shipping option.
     *
     * @var string|int
     */
    public $chosenShipping = null;

    /**
     * Whether the guest wants to sign up as a new user
     *
     * @var boolean|null
     */
    public ?bool $signup = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    /**
     * The checkout steps.
     *
     * @var array
     */
    public array $steps = [
        'shipping_address' => 1,
        'shipping_option' => 2,
        'billing_address' => 3,
        'payment' => 4,
    ];

    /**
     * The payment type we want to use.
     *
     * @var string
     */
    public $paymentType = 'card';

    /**
     * {@inheritDoc}
     */
    protected $listeners = [
        'cartUpdated' => 'refreshCart',
        'selectedShippingOption' => 'refreshCart',
        'selectedShippingOption' => 'refreshCart',
    ];

    public $payment_intent = null;

    public $payment_intent_client_secret = null;

    protected $queryString = [
        'payment_intent',
        'payment_intent_client_secret',
    ];

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return array_merge(
            $this->getAddressValidation('shipping'),
            $this->getAddressValidation('billing'),
            [
                'shippingIsBilling' => 'boolean',
                'chosenShipping' => 'required',
                'signup' => 'boolean',
                'password' => ['required', 'string', new Password, 'confirmed']
            ]
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function mount()
    {
        if (!$this->cart = CartSession::current()) {
            $this->redirect('/');

            return;
        }

        if (!Auth::user()) {
            $this->steps['signup'] = 4;
            $this->steps['payment'] = 5;
            $this->signup = session('guest-checkout-signup', false);
        }

        if ($this->cart && Auth::user() && !$this->cart->user) {
            CartSession::associate($this->cart, Auth::user(), 'merge');
            $this->cart->user_id = Auth::user()->id;
            $this->cart->save();
        }

        if ($this->payment_intent) {
            $payment = Payments::driver($this->paymentType)->cart($this->cart)->withData([
                'payment_intent_client_secret' => $this->payment_intent_client_secret,
                'payment_intent' => $this->payment_intent,
            ])->authorize();

            if ($payment->success) {
                redirect()->route('checkout-success.view');

                return;
            }
        }

        // Do we have a shipping address?
        $userShippingAddress = $this->cart->user?->customers->first()->addresses()->where('shipping_default', true)->first();
        if ($userShippingAddress) {
            $this->cart->getManager()->setShippingAddress($userShippingAddress);
            $this->cart->save();
        }
        $this->shipping = $this->cart->shippingAddress ?: new CartAddress;

        // What about a billing address?
        $userBillingAddress = $this->cart->user?->customers->first()->addresses()->where('billing_default', true)->first();
        if ($userBillingAddress) {
            $this->cart->getManager()->setBillingAddress($userBillingAddress);
            $this->cart->save();
        }
        $this->billing = $this->cart->billingAddress ?: new CartAddress;

        $this->determineCheckoutStep();
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate()
    {
        $this->cart = CartSession::getCart();
    }

    /**
     * Trigger an event to refresh addresses.
     *
     * @return void
     */
    public function triggerAddressRefresh()
    {
        $this->emit('refreshAddress');
    }

    /**
     * Determines what checkout step we should be at.
     *
     * @return void
     */
    public function determineCheckoutStep()
    {
        $shippingAddress = $this->cart->shippingAddress;
        $billingAddress = $this->cart->billingAddress;

        if ($shippingAddress) {
            if ($shippingAddress->id) {
                $this->currentStep = $this->steps['shipping_address'] + 1;
            }

            // Do we have a selected option?
            if ($this->shippingOption) {
                $this->chosenShipping = $this->shippingOption->getIdentifier();
                $this->currentStep = $this->steps['shipping_option'] + 1;
            } else {
                $this->currentStep = $this->steps['shipping_option'];
                $this->chosenShipping = $this->shippingOptions->first()?->getIdentifier();

                return;
            }
        }

        if ($billingAddress) {
            $this->currentStep = $this->steps['billing_address'] + 1;

            if (isset($this->steps['signup'])) {
                if ($this->signup = session('guest-checkout-signup', null) === null) {
                    $this->currentStep = $this->steps['signup'];
                } else {
                    $this->currentStep = $this->steps['signup'] + 1;
                }
            }
        }

    }

    /**
     * Refresh the cart instance.
     *
     * @return void
     */
    public function refreshCart()
    {
        $this->cart = CartSession::getCart();
    }

    /**
     * Return the shipping option.
     *
     * @return void
     */
    public function getShippingOptionProperty()
    {
        $shippingAddress = $this->cart->shippingAddress;

        if (!$shippingAddress) {
            return;
        }

        if ($option = $shippingAddress->shipping_option) {
            return ShippingManifest::getOptions($this->cart)->first(function ($opt) use ($option) {
                return $opt->getIdentifier() == $option;
            });
        }

        return null;
    }

    /**
     * Save the address for a given type.
     *
     * @param  string  $type
     * @return void
     */
    public function saveAddress($type)
    {
        $validatedData = $this->validate(
            $this->getAddressValidation($type)
        );

        $address = $this->{$type};

        if ($type == 'billing') {
            $this->cart->getManager()->setBillingAddress($address);
        }

        if ($type == 'shipping') {
            $this->cart->getManager()->setShippingAddress($address);
            if ($this->shippingIsBilling) {
                // Do we already have a billing address?
                if ($billing = $this->cart->billingAddress) {
                    $billing->fill($validatedData['shipping']);
                    $this->cart->getManager()->setBillingAddress($billing);
                } else {
                    $address = $address->only(
                        $address->getFillable()
                    );
                    $this->cart->getManager()->setBillingAddress($address);
                }

                $this->billing = $this->cart->billingAddress;
            }
        }

        $this->determineCheckoutStep();
    }

    /**
     * Sign up a new user, or skip if $signup is false
     *
     * @return void
     */
    public function saveUser(CreateNewUser $createNewUser)
    {
        if ($this->signup) {
            session()->put('guest-checkout-signup', true);
            $address = $this->cart->shippingAddress ?? $this->cart->billingAddress;
            $name = $address->first_name;
            if ($address->last_name) $name .= " {$address->last_name}";
            $user = $createNewUser->create([
                'name' => $name,
                'email' => $address->contact_email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ]);

            $user->customers->first()->addresses()->create([
                'first_name' => $this->cart->shippingAddress->first_name,
                'last_name' => $this->cart->shippingAddress->last_name,
                'line_one' => $this->cart->shippingAddress->line_one,
                'line_two' => $this->cart->shippingAddress->line_two,
                'line_three' => $this->cart->shippingAddress->line_three,
                'city' => $this->cart->shippingAddress->city,
                'state' => $this->cart->shippingAddress->state,
                'postcode' => $this->cart->shippingAddress->postcode,
                'country_id' => $this->cart->shippingAddress->country_id,
                'contact_email' => $this->cart->shippingAddress->contact_email,
                'contact_phone' => $this->cart->shippingAddress->contact_phone,
                'shipping_default' => true,
            ]);
            $user->customers->first()->addresses()->create([
                'first_name' => $this->cart->billingAddress->first_name,
                'last_name' => $this->cart->billingAddress->last_name,
                'line_one' => $this->cart->billingAddress->line_one,
                'line_two' => $this->cart->billingAddress->line_two,
                'line_three' => $this->cart->billingAddress->line_three,
                'city' => $this->cart->billingAddress->city,
                'state' => $this->cart->billingAddress->state,
                'postcode' => $this->cart->billingAddress->postcode,
                'country_id' => $this->cart->billingAddress->country_id,
                'contact_email' => $this->cart->billingAddress->contact_email,
                'contact_phone' => $this->cart->billingAddress->contact_phone,
                'billing_default' => true,
            ]);

            Auth::login($user);
        }
        else session()->put('guest-checkout-signup', false);

        $this->determineCheckoutStep();
    }

    /**
     * Save the selected shipping option.
     *
     * @return void
     */
    public function saveShippingOption()
    {
        $option = $this->shippingOptions->first(fn ($option) => $option->getIdentifier() == $this->chosenShipping);

        CartSession::current()->getManager()->setShippingOption($option);

        $this->refreshCart();

        $this->determineCheckoutStep();
    }

    public function checkout()
    {
        $payment = Payments::cart($this->cart)->withData([
            'payment_intent_client_secret' => $this->payment_intent_client_secret,
            'payment_intent' => $this->payment_intent,
        ]);
        
        if ($this->paymentType == 'cash') {
            $payment->setConfig([
                'authorized' => 'payment-offline',
            ]);
        }

        $payment->authorize();

        session()->put('guest-checkout-signup', null);

        if ($payment->success ?? false) {
            redirect()->route('checkout-success.view');

            return;
        }

        return redirect()->route('checkout-success.view');
    }

    /**
     * Return the available countries.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCountriesProperty()
    {
        return Country::whereIn('iso3', ['GBR', 'USA'])->get();
    }

    /**
     * Return available shipping options.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getShippingOptionsProperty()
    {
        return ShippingManifest::getOptions(
            CartSession::current()
        );
    }

    /**
     * Return the address validation rules for a given type.
     *
     * @param  string  $type
     * @return array
     */
    protected function getAddressValidation($type)
    {
        return [
            "{$type}.first_name" => 'required',
            "{$type}.last_name" => 'required',
            "{$type}.line_one" => 'required',
            "{$type}.country_id" => 'required',
            "{$type}.city" => 'required',
            "{$type}.postcode" => 'required',
            "{$type}.company_name" => 'nullable',
            "{$type}.line_two" => 'nullable',
            "{$type}.line_three" => 'nullable',
            "{$type}.state" => 'nullable',
            "{$type}.delivery_instructions" => 'nullable',
            "{$type}.contact_email" => 'required|email',
            "{$type}.contact_phone" => 'nullable',
        ];
    }

    public function render()
    {
        return view('livewire.checkout-page')
            ->layout('layouts.checkout');
    }
}
