<?php

namespace App\Livewire;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Facades\Payments;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Lunar\Models\Country;

class CheckoutPage extends Component
{
    /**
     * The Cart instance.
     */
    public ?Cart $cart;

    /**
     * The shipping address instance.
     */
    public ?CartAddress $shipping = null;

    /**
     * The billing address instance.
     */
    public ?CartAddress $billing = null;

    /**
     * The current checkout step.
     */
    public int $currentStep = 1;

    /**
     * Whether the shipping address is the billing address too.
     */
    public bool $shippingIsBilling = true;

    /**
     * The chosen shipping option.
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
     */
    public array $steps = [
        'shipping_address' => 1,
        'shipping_option' => 2,
        'billing_address' => 3,
        'payment' => 4,
    ];

    /**
     * The payment type we want to use.
     */
    public string $paymentType = 'card';

    /**
     * {@inheritDoc}
     */
    protected $listeners = [
        'cartUpdated' => 'refreshCart',
        'selectedShippingOption' => 'refreshCart',
    ];

    public $payment_intent = null;

    public $payment_intent_client_secret = null;

    public $paypal_order_id = null;

    protected $queryString = [
        'payment_intent',
        'payment_intent_client_secret',
        'paypal_order_id',
    ];

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return array_merge(
            $this->getAddressValidation('shipping'),
            $this->getAddressValidation('billing'),
            [
                'shippingIsBilling' => 'boolean',
                'chosenShipping' => 'required',
                'signup' => 'boolean',
                'password' => ['required', 'string', new Password(8), 'confirmed']
            ]
        );
    }

    public function mount(): void
    {
        if (! $this->cart = CartSession::current()) {
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
            $payment = Payments::driver($this->paymentType)
                ->cart($this->cart)
                ->withData([
                    'payment_intent_client_secret' => $this->payment_intent_client_secret,
                    'payment_intent' => $this->payment_intent,
                ])->authorize();

            if ($payment?->success) {
                if ($payment->orderId) {
                    session()->put('last_order_id', $payment->orderId);
                }
                redirect()->route('checkout-success.view');

                return;
            }
        }

        if ($this->paypal_order_id) {
            $payment = Payments::driver('paypal')
                ->cart($this->cart)
                ->withData([
                    'paypal_order_id' => $this->paypal_order_id,
                ])->authorize();

            if ($payment?->success) {
                if ($payment->orderId) {
                    session()->put('last_order_id', $payment->orderId);
                }
                redirect()->route('checkout-success.view');

                return;
            } else {
                dd($payment);
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

    public function hydrate(): void
    {
        $this->cart = CartSession::current();
    }

    /**
     * Trigger an event to refresh addresses.
     */
    public function triggerAddressRefresh(): void
    {
        $this->dispatch('refreshAddress');
    }

    /**
     * Determines what checkout step we should be at.
     */
    public function determineCheckoutStep(): void
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
                $this->signup = session('guest-checkout-signup', null);
                if ($this->signup === null) {
                    $this->currentStep = $this->steps['signup'];
                } else {
                    $this->currentStep = $this->steps['signup'] + 1;
                }
            }
        }
    }

    /**
     * Refresh the cart instance.
     */
    public function refreshCart(): void
    {
        $this->cart = CartSession::current();
    }

    /**
     * Return the shipping option.
     */
    public function getShippingOptionProperty()
    {
        $shippingAddress = $this->cart->shippingAddress;

        if (! $shippingAddress) {
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
     */
    public function saveAddress(string $type): void
    {
        $validatedData = $this->validate(
            $this->getAddressValidation($type)
        );

        $address = $this->{$type};

        if ($type == 'billing') {
            $this->cart->setBillingAddress($address);
            $this->billing = $this->cart->billingAddress;
        }

        if ($type == 'shipping') {
            $this->cart->setShippingAddress($address);
            $this->shipping = $this->cart->shippingAddress;

            if ($this->shippingIsBilling) {
                // Do we already have a billing address?
                if ($billing = $this->cart->billingAddress) {
                    $billing->fill($validatedData['shipping']);
                    $this->cart->setBillingAddress($billing);
                } else {
                    $address = $address->only(
                        $address->getFillable()
                    );
                    $this->cart->setBillingAddress($address);
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
        } else session()->put('guest-checkout-signup', false);

        $this->determineCheckoutStep();
    }

    /**
     * Save the selected shipping option.
     */
    public function saveShippingOption(): void
    {
        $option = $this->shippingOptions->first(fn($option) => $option->getIdentifier() == $this->chosenShipping);

        CartSession::setShippingOption($option);

        $this->refreshCart();

        $this->determineCheckoutStep();
    }

    public function checkout()
    {
        $paymentData = [];
        $paymentConfig = [];
        if ($this->paymentType == 'stripe') {
            $paymentData = [
                'payment_intent_client_secret' => $this->payment_intent_client_secret,
                'payment_intent' => $this->payment_intent,
            ];
        } else if ($this->paymentType == 'paypal') {
            $paymentData = [
                'paypal_order_id' => $this->paypal_order_id,
            ];
        }
        $payment = Payments::driver($this->paymentType)
            ->cart($this->cart)
            ->withData($paymentData)
            ->setConfig($paymentConfig)
            ->authorize();

        session()->put('guest-checkout-signup', null);

        if ($payment?->success) {
            if ($payment->orderId) {
                session()->put('last_order_id', $payment->orderId);
            }
            return redirect()->route('checkout-success.view');
        }

        return redirect()->route('checkout.view')->with('error', 'Payment authorization failed. Please try again.');
    }

    /**
     * Return the available countries.
     */
    public function getCountriesProperty(): Collection
    {
        return Country::whereIn('iso3', ['GBR', 'USA'])->get();
    }

    /**
     * Return available shipping options.
     */
    public function getShippingOptionsProperty(): Collection
    {
        return ShippingManifest::getOptions(
            $this->cart
        );
    }

    /**
     * Return the address validation rules for a given type.
     */
    protected function getAddressValidation(string $type): array
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

    public function render(): View
    {
        return view('livewire.checkout-page')
            ->layout('layouts.checkout');
    }
}
