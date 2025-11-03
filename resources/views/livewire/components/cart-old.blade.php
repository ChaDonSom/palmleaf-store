<div
    class="relative"
    x-data="{
        linesVisible: @entangle('linesVisible')
    }"
>
    <button
        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium transition bg-white border rounded-2xl border-slate-300 hover:bg-slate-50"
        x-on:click="linesVisible = !linesVisible"
    >
        <x-icons.shopping-cart />
        <span class="hidden md:inline">Cart</span>
        @if (count($lines) > 0)
            <span class="ml-1 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-slate-900 px-1.5 text-[10px] font-medium text-white">
                {{ count($lines) }}
            </span>
        @endif
    </button>

    <div
        class="absolute right-0 top-auto z-50 mt-2 w-[95vw] sm:w-[480px] bg-white border border-slate-100 shadow-xl rounded-2xl"
        x-show="linesVisible"
        x-on:click.away="linesVisible = false"
        x-transition
        x-cloak
    >
        <div class="p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Your Cart</h3>
                <button
                    class="transition-transform text-slate-500 hover:scale-110"
                    type="button"
                    x-on:click="linesVisible = false"
                >
                    <x-icons.close />
                </button>
            </div>

            @if ($this->cart)
                @if ($lines)
                    <div class="space-y-4 overflow-y-auto max-h-96">
                        @foreach ($lines as $index => $line)
                            <div class="flex items-center gap-4" wire:key="line_{{ $line['id'] }}">
                                <img
                                    class="object-cover w-20 h-20 shadow-sm rounded-xl"
                                    src="{{ $line['thumbnail'] }}"
                                    alt="{{ $line['description'] }}"
                                />
                                <div class="flex-1">
                                    <div class="text-sm font-semibold">{{ $line['description'] }}</div>
                                    <div class="text-sm text-slate-500">{{ $line['unit_price'] }} • Qty {{ $line['quantity'] }}</div>
                                    <div class="mt-1 text-xs text-slate-400">{{ $line['identifier'] }} / {{ $line['options'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold">{{ $line['sub_total'] }}</div>
                                    <button
                                        type="button"
                                        wire:click="removeLine('{{ $line['id'] }}')"
                                        class="mt-1 text-xs transition text-slate-500 hover:text-red-600"
                                    >
                                        Remove
                                    </button>
                                </div>
                            </div>

                            @if ($errors->get('lines.' . $index . '.quantity'))
                                <div class="p-2 text-xs font-medium text-center text-red-700 rounded bg-red-50" role="alert">
                                    @foreach ($errors->get('lines.' . $index . '.quantity') as $error)
                                        {{ $error }}
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between pt-4 mt-4 border-t border-slate-100">
                        <div class="text-sm text-slate-500">Subtotal</div>
                        <div class="text-lg font-bold">{{ $this->cart->subTotal->formatted() }}</div>
                    </div>

                    <a
                        href="{{ route('checkout.view') }}"
                        class="flex items-center justify-center w-full gap-2 px-6 py-3 mt-4 text-sm font-medium text-white transition rounded-2xl bg-slate-900 hover:bg-slate-800"
                    >
                        Checkout
                        <x-icons.arrow-right />
                    </a>

                    <div class="mt-3 text-xs text-center text-slate-500">
                        Taxes and shipping calculated at checkout.
                    </div>
                @else
                    <div class="p-6 text-center border rounded-xl border-slate-100 text-slate-500">
                        Your cart is empty.
                    </div>
                @endif
            @else
                <div class="p-6 text-center border rounded-xl border-slate-100 text-slate-500">
                    Your cart is empty.
                </div>
            @endif
        </div>
    </div>
</div>
