<div
    class="relative"
    x-data="{
        linesVisible: @entangle('linesVisible')
    }"
>
    <button
        class="inline-flex items-center gap-2 rounded-2xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium hover:bg-slate-50 transition"
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
                    class="text-slate-500 transition-transform hover:scale-110"
                    type="button"
                    x-on:click="linesVisible = false"
                >
                    <x-icons.close />
                </button>
            </div>

            @if ($this->cart)
                @if ($lines)
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach ($lines as $index => $line)
                            <div class="flex items-center gap-4" wire:key="line_{{ $line['id'] }}">
                                <img
                                    class="h-20 w-20 rounded-xl object-cover shadow-sm"
                                    src="{{ $line['thumbnail'] }}"
                                    alt="{{ $line['description'] }}"
                                />
                                <div class="flex-1">
                                    <div class="font-semibold text-sm">{{ $line['description'] }}</div>
                                    <div class="text-sm text-slate-500">{{ $line['unit_price'] }} • Qty {{ $line['quantity'] }}</div>
                                    <div class="text-xs text-slate-400 mt-1">{{ $line['identifier'] }} / {{ $line['options'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold">{{ $line['sub_total'] }}</div>
                                    <button
                                        type="button"
                                        wire:click="removeLine('{{ $line['id'] }}')"
                                        class="mt-1 text-xs text-slate-500 hover:text-red-600 transition"
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

                    <div class="flex items-center justify-between border-t border-slate-100 pt-4 mt-4">
                        <div class="text-sm text-slate-500">Subtotal</div>
                        <div class="text-lg font-bold">{{ $this->cart->subTotal->formatted() }}</div>
                    </div>

                    <a 
                        href="{{ route('checkout.view') }}"
                        class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-sm font-medium text-white hover:bg-slate-800 transition"
                    >
                        Checkout
                        <x-icons.arrow-right />
                    </a>
                    
                    <div class="text-center text-xs text-slate-500 mt-3">
                        Taxes and shipping calculated at checkout.
                    </div>
                @else
                    <div class="rounded-xl border border-slate-100 p-6 text-center text-slate-500">
                        Your cart is empty.
                    </div>
                @endif
            @else
                <div class="rounded-xl border border-slate-100 p-6 text-center text-slate-500">
                    Your cart is empty.
                </div>
            @endif
        </div>
    </div>
</div>
