<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <x-form-section submit="">
            <x-slot name="title">{{ __('Order') }} {{ $order->reference }}</x-slot>
            <x-slot name="description">
                <div>
                    {{ $order->status }}
                </div>
                <div>
                    {{ $order->total->formatted }}
                </div>
            </x-slot>
            <x-slot name="form">
                <div class="col-span-12 space-y-4">
                    <h3 class="font-medium">{{ __('Order Summary') }}</h3>

                    <div class="bg-white border border-gray-100 rounded-xl px-6 py-8">
                    <div class="flow-root">
                        <div class="-my-4 divide-y divide-gray-100">
                            @foreach ($order->lines->where('purchasable_type', '!=', 'Lunar\\DataTypes\\ShippingOption') as $line)
                                <div
                                    class="flex items-center py-4"
                                    wire:key="cart_line_{{ $line->id }}"
                                >
                                    <img
                                        class="object-cover w-16 h-16 rounded-lg"
                                        src="{{ $line->purchasable->getThumbnail()?->original_url }}"
                                    />

                                    <div class="flex-1 ml-4">
                                        <p class="text-sm font-medium max-w-[35ch]">
                                            {{ $line->purchasable->getDescription() }}
                                        </p>

                                        <span class="block mt-1 text-xs text-gray-500">
                                            {{ $line->quantity }} @ {{ $line->sub_total->formatted() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flow-root">
                        <dl class="-my-4 text-sm divide-y divide-gray-100">
                            <div class="flex flex-wrap py-4">
                                <dt class="w-1/2 font-medium">
                                    Sub Total
                                </dt>

                                <dd class="w-1/2 text-right">
                                    {{ $order->sub_total->formatted() }}
                                </dd>
                            </div>

                            @if ($shippingLine = $order->lines()->where('purchasable_type', 'Lunar\\DataTypes\\ShippingOption')->first())
                                <div class="flex flex-wrap py-4">
                                    <dt class="w-1/2 font-medium">
                                        {{ $shippingLine->description }}
                                    </dt>

                                    <dd class="w-1/2 text-right">
                                        {{ $shippingLine->total->formatted }}
                                    </dd>
                                </div>
                            @endif

                            @foreach ($order->tax_breakdown as $tax)
                                <div class="flex flex-wrap py-4">
                                    <dt class="w-1/2 font-medium">
                                        {{ $tax->description }}
                                    </dt>

                                    <dd class="w-1/2 text-right">
                                        {{ $tax->total->formatted() }}
                                    </dd>
                                </div>
                            @endforeach

                            <div class="flex flex-wrap py-4">
                                <dt class="w-1/2 font-medium">
                                    Total
                                </dt>

                                <dd class="w-1/2 text-right">
                                    {{ $order->total->formatted() }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                    </div>
                </div>

                <div class="col-span-12 space-y-4">
                    <h3>{{ __('Shipping address') }}</h3>
                    @include('partials.order.address', [
                        'type' => 'shippingAddress',
                    ])
                </div>
                <div class="col-span-12 space-y-4">
                    <h3>{{ __('Billing address') }}</h3>
                    @include('partials.order.address', [
                        'type' => 'billingAddress',
                    ])
                </div>
            </x-slot>
            <x-slot name="actions">
            </x-slot>
        </x-form-section>
    </div>
</x-app-layout>