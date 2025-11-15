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

                {{-- Mobile: Paginated Grid with Carousel Navigation --}}
                @php
                    $imagesPerPage = 6; // 3 rows of 2 columns
                    $totalPages = ceil(count($this->images) / $imagesPerPage);
                @endphp
                <div class="sm:hidden" x-data="{ currentPage: 0, totalPages: {{ $totalPages }}, imagesPerPage: {{ $imagesPerPage }} }">
                    <div class="relative">
                        {{-- Grid container --}}
                        <div class="overflow-hidden">
                            <div 
                                class="flex transition-transform duration-300 ease-in-out"
                                :style="'transform: translateX(-' + (currentPage * 100) + '%)'">
                                @for ($page = 0; $page < $totalPages; $page++)
                                    <div class="w-full flex-shrink-0 grid grid-cols-2 gap-4 px-1">
                                        @foreach ($this->images->slice($page * $imagesPerPage, $imagesPerPage) as $image)
                                            <div
                                                class="aspect-w-1 aspect-h-1 cursor-pointer rounded-xl {{ $image->id === $imageId ? 'brightness-90' : '' }} hover:brightness-90 transition"
                                                wire:key="image_mobile_{{ $image->id }}"
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
                                @endfor
                            </div>
                        </div>

                        {{-- Previous button --}}
                        <button
                            @click="currentPage = Math.max(0, currentPage - 1)"
                            x-show="currentPage > 0"
                            class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg transition z-10"
                            type="button"
                            aria-label="Previous page"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        {{-- Next button --}}
                        <button
                            @click="currentPage = Math.min(totalPages - 1, currentPage + 1)"
                            x-show="currentPage < totalPages - 1"
                            class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg transition z-10"
                            type="button"
                            aria-label="Next page"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    {{-- Page indicators (only show if more than 1 page) --}}
                    <div class="flex justify-center gap-2 mt-3" x-show="totalPages > 1">
                        <template x-for="page in totalPages" :key="page">
                            <button
                                @click="currentPage = page - 1"
                                class="w-2 h-2 rounded-full transition"
                                :class="currentPage === (page - 1) ? 'bg-gray-800' : 'bg-gray-300'"
                                type="button"
                            >
                                <span class="sr-only" x-text="'Go to page ' + page"></span>
                            </button>
                        </template>
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
