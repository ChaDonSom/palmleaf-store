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

                {{-- Desktop: Grid layout --}}
                <div class="hidden sm:grid grid-cols-2 gap-4 sm:grid-cols-4">
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

                {{-- Mobile: Carousel --}}
                <div class="sm:hidden" x-data="{ currentIndex: 0, totalImages: {{ count($this->images) }} }">
                    <div class="relative">
                        {{-- Carousel container --}}
                        <div class="overflow-hidden">
                            <div 
                                class="flex transition-transform duration-300 ease-in-out gap-2"
                                :style="'transform: translateX(-' + (currentIndex * 100) + '%)'">
                                @foreach ($this->images as $image)
                                    <div
                                        class="w-full flex-shrink-0 cursor-pointer"
                                        wire:key="image_mobile_{{ $image->id }}"
                                        wire:click="$set('imageId', {{ $image->id }})"
                                    >
                                        <div class="aspect-w-1 aspect-h-1 rounded-xl {{ $image->id === $imageId ? 'brightness-90' : '' }} hover:brightness-90 transition">
                                            <img
                                                loading="lazy"
                                                class="object-cover rounded-xl"
                                                src="{{ $image->getUrl('small') }}"
                                                alt="{{ $this->product->translateAttribute('name') }}"
                                            />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Previous button --}}
                        <button
                            @click="currentIndex = Math.max(0, currentIndex - 1)"
                            x-show="currentIndex > 0"
                            class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg transition"
                            type="button"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        {{-- Next button --}}
                        <button
                            @click="currentIndex = Math.min(totalImages - 1, currentIndex + 1)"
                            x-show="currentIndex < totalImages - 1"
                            class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg transition"
                            type="button"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    {{-- Carousel indicators --}}
                    <div class="flex justify-center gap-2 mt-3">
                        @foreach ($this->images as $index => $image)
                            <button
                                @click="currentIndex = {{ $index }}"
                                class="w-2 h-2 rounded-full transition"
                                :class="currentIndex === {{ $index }} ? 'bg-gray-800' : 'bg-gray-300'"
                                type="button"
                            >
                                <span class="sr-only">Go to image {{ $index + 1 }}</span>
                            </button>
                        @endforeach
                    </div>
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
