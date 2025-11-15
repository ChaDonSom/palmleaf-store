<div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl">
    <div class="flex items-center h-16 px-6 border-b border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-medium dark:text-white">
            Payment
        </h3>
    </div>

    @if ($currentStep >= $step)
        <div class="p-6 space-y-4">
            <div class="flex gap-4">
                <button
                    @class([
                        'px-5 py-2 text-sm border font-medium rounded-3xl',
                        'text-slate-900 border-yellow-600 bg-yellow-50' => $paymentType === 'card',
                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 dark:border-gray-600' => $paymentType !== 'card',
                    ])
                    type="button"
                    wire:click.prevent="$set('paymentType', 'card')"
                >
                    Pay by card
                </button>

                <button
                    @class([
                        'px-5 py-2 text-sm border font-medium rounded-3xl',
                        'text-slate-900 border-yellow-600 bg-yellow-50' => $paymentType === 'paypal',
                        'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 dark:border-gray-600' => $paymentType !== 'paypal',
                    ])
                    type="button"
                    wire:click.prevent="$set('paymentType', 'paypal')"
                >
                    Pay with PayPal
                </button>
            </div>

            @if ($paymentType == 'card')
                <livewire:payment-form
                    :cart="$cart"
                    :returnUrl="route('checkout.view')"
                />
            @endif

            @if ($paymentType == 'paypal')
                <livewire:paypal-payment-form
                    :cart="$cart"
                    :returnUrl="route('checkout.view')"
                />
            @endif
        </div>
    @endif
</div>
