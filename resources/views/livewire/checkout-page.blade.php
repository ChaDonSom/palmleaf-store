<x-slot:head>
    @php
    $paypalClientId = config('paypal.' . config('paypal.mode', 'sandbox') . '.client_id');
    $policy = config('lunar.paypal.policy', 'automatic');
    $intentIfManual = '';
    if ($policy == 'manual') {
        $intentIfManual = '&intent=authorize';
    }
    @endphp
    <script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}{{ $intentIfManual }}"></script>
</x-slot>

<div>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 lg:items-start">
            <div class="px-6 py-8 space-y-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 lg:sticky lg:top-8 rounded-xl lg:order-last">
                <h3 class="font-medium dark:text-white">
                    Order Summary
                </h3>

                <div class="flow-root">
                    <div class="-my-4 divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($cart->lines as $line)
                            <div
                                class="flex items-center py-4"
                                wire:key="cart_line_{{ $line->id }}"
                            >
                                <img
                                    class="object-cover w-16 h-16 rounded-lg"
                                    src="{{ $line->purchasable->getThumbnail()?->original_url }}"
                                />

                                <div class="flex-1 ml-4">
                                    <p class="text-sm font-medium max-w-[35ch] dark:text-gray-100">
                                        {{ $line->purchasable->getDescription() }}
                                    </p>

                                    <span class="block mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ $line->quantity }} @ {{ $line->unitPrice->formatted() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Discount Code Section --}}
                <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                    <h4 class="mb-3 text-sm font-medium dark:text-white">Discount Code</h4>

                    @if($cart->coupon_code)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-sm font-medium text-green-900">{{ $cart->coupon_code }}</span>
                            </div>
                            <button
                                wire:click="removeCoupon"
                                type="button"
                                class="text-xs text-red-600 hover:text-red-700"
                            >
                                Remove
                            </button>
                        </div>
                    @else
                        <div class="space-y-2">
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    wire:model.live="couponCode"
                                    placeholder="Enter code"
                                    class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-full focus:ring-2 focus:ring-sky-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                                >
                                <button
                                    wire:click="applyCoupon"
                                    type="button"
                                    class="px-4 py-2 text-sm font-medium transition rounded-full bg-sky-200 hover:bg-sky-300"
                                >
                                    Apply
                                </button>
                            </div>

                            @if($couponMessage)
                                <p class="text-xs {{ $couponSuccess ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $couponMessage }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flow-root">
                    <dl class="-my-4 text-sm divide-y divide-gray-100 dark:divide-gray-700">
                        <div class="flex flex-wrap py-4">
                            <dt class="w-1/2 font-medium dark:text-gray-300">
                                Sub Total
                            </dt>

                            <dd class="w-1/2 text-right dark:text-gray-100">
                                {{ $cart->subTotal->formatted() }}
                            </dd>
                        </div>

                        @if ($cart->discountTotal && $cart->discountTotal->value > 0)
                            <div class="flex flex-wrap py-4">
                                <dt class="w-1/2 font-medium text-green-600">
                                    Discount
                                </dt>

                                <dd class="w-1/2 text-right text-green-600">
                                    -{{ $cart->discountTotal->formatted() }}
                                </dd>
                            </div>
                        @endif

                        @if ($this->shippingOption)
                            <div class="flex flex-wrap py-4">
                                <dt class="w-1/2 font-medium dark:text-gray-300">
                                    {{ $this->shippingOption->getDescription() }}
                                </dt>

                                <dd class="w-1/2 text-right dark:text-gray-100">
                                    {{ $this->shippingOption->getPrice()->formatted() }}
                                </dd>
                            </div>
                        @endif

                        @foreach ($cart->taxBreakdown->amounts as $tax)
                            <div class="flex flex-wrap py-4">
                                <dt class="w-1/2 font-medium dark:text-gray-300">
                                    {{ $tax->description }}
                                </dt>

                                <dd class="w-1/2 text-right dark:text-gray-100">
                                    {{ $tax->price->formatted() }}
                                </dd>
                            </div>
                        @endforeach

                        <div class="flex flex-wrap py-4">
                            <dt class="w-1/2 font-medium dark:text-gray-300">
                                Total
                            </dt>

                            <dd class="w-1/2 text-right dark:text-gray-100">
                                {{ $cart->total->formatted() }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="space-y-6 lg:col-span-2">
                @include('partials.checkout.address', [
                    'type' => 'shipping',
                    'step' => $steps['shipping_address'],
                ])

                @include('partials.checkout.shipping_option', [
                    'step' => $steps['shipping_option'],
                ])

                @include('partials.checkout.address', [
                    'type' => 'billing',
                    'step' => $steps['billing_address'],
                ])

                @auth
                @else
                    @include('partials.checkout.signup', [
                        'step' => $steps['signup']
                    ])
                @endauth

                @include('partials.checkout.payment', [
                    'step' => $steps['payment']
                ])
            </div>
        </div>
    </div>
</div>
