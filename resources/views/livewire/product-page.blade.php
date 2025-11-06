<section>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <div class="grid items-start grid-cols-1 gap-8 md:grid-cols-2">
            <div class="grid grid-cols-2 gap-4 md:grid-cols-1">
                @if ($this->image)
                    <div class="aspect-w-1 aspect-h-1">
                        <img
                            class="object-cover rounded-xl"
                            src="{{ $this->image->getUrl('large') }}"
                            alt="{{ $this->product->translateAttribute('name') }}"
                        />
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($this->images as $image)
                        <div
                            class="aspect-w-1 aspect-h-1 cursor-pointer rounded-xl {{ $image->id === $imageId ? 'brightness-90' : '' }} hover:brightness-90 transition"
                            wire:key="image_{{ $image->id }}"
                            wire:click="$set('imageId', {{ $image->id }})"
                        >
                            <img
                                loading="lazy"
                                class="object-cover rounded-xl"
                                src="{{ $image->getUrl('small') }}"
                                alt="{{ $this->product->translateAttribute('name') }}"
                            />
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold">
                        {{ $this->product->translateAttribute('name') }}
                    </h1>

                    <x-product-price
                        class="ml-4 font-medium"
                        :variant="$this->variant"
                    />
                </div>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $this->variant->sku }}
                </p>

                <article class="mt-4 text-gray-700">
                    {!! $this->product->translateAttribute('description') !!}
                </article>

                <form class="mt-4">
                    <div class="space-y-4">
                        @foreach ($this->productOptions as $option)
                            <fieldset>
                                <legend class="text-xs font-medium text-gray-700">
                                    {{ $option['option']->translate('name') }}
                                </legend>

                                <div class="flex flex-wrap gap-2 mt-2 text-xs tracking-wide uppercase">
                                    @foreach ($option['values'] as $value)
                                        @php
                                            $isSelected = isset($selectedOptionValues[$option['option']->id]) &&
                                                         $selectedOptionValues[$option['option']->id] == $value->id;
                                        @endphp
                                        <button
                                            class="px-6 py-4 font-medium border rounded-3xl focus:outline-none transition {{ $isSelected ? 'bg-yellow-500 border-yellow-600 text-white hover:bg-yellow-400' : 'border-gray-300 hover:bg-gray-100' }}"
                                            type="button"
                                            wire:click="$set('selectedOptionValues.{{ $option['option']->id }}', {{ $value->id }})"
                                        >
                                            {{ $value->translate('name') }}
                                        </button>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endforeach
                    </div>

                    <div class="max-w-xs mt-8">
                        <livewire:components.add-to-cart
                            :purchasable="$this->variant"
                            :wire:key="$this->variant->id"
                        >
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
