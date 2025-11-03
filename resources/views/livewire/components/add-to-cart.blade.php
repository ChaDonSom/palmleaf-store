<div>
    <div class="flex gap-4">
        <div>
            <label
                for="quantity"
                class="sr-only"
            >
                Quantity
            </label>

            <x-input.text
                class="w-16 px-1 py-4 text-sm text-center transition border border-gray-100 rounded-[32px] no-spinners focus:ring-green-600"
                type="number"
                id="quantity"
                min="1"
                value="1"
                wire:model.live="quantity"
                onfocus="this.select()"{{-- TODO: new demo doesn't have this --}}
            />
        </div>

        <button
            type="submit"
            class="w-full px-6 py-4 text-sm font-medium text-center text-white bg-green-700 rounded-[32px] hover:bg-green-800"
            wire:click.prevent="addToCart"
        >
            Add to Cart
        </button>
    </div>

    @if ($errors->has('quantity'))
        <div
            class="p-2 mt-4 text-xs font-medium text-center text-red-700 rounded bg-red-50"
            role="alert"
        >
            @foreach ($errors->get('quantity') as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif
</div>
