<div x-data="{ imageLoaded: false }" x-init="
    const img = new Image();
    img.onload = () => {
        setTimeout(() => { imageLoaded = true; }, 100);
    };
    img.src = 'https://images.unsplash.com/photo-1557308970-df80a9ccee84?q=80&w=1600&auto=format&fit=crop';
">
    <!-- Main Content -->
    <div class="min-h-screen bg-gradient-to-b from-white to-blue-50 text-slate-800">
        <!-- Hero Section -->
        <section class="relative overflow-hidden border-b">
            <!-- Background Image with Fade -->
            <div
                class="absolute inset-0 transition duration-1000 ease-out bg-center bg-cover transform-gpu"
                :class="imageLoaded ? 'opacity-100 scale-[1.025]' : 'opacity-0 scale-100'"
                style="background-image: url('https://images.unsplash.com/photo-1557308970-df80a9ccee84?q=80&w=1600&auto=format&fit=crop'); will-change: transform, opacity;"
            ></div>

            <!-- Content -->
            <div class="relative px-4 py-16 mx-auto max-w-7xl md:py-24">
            <div class="max-w-2xl p-8 mx-auto text-center shadow-xl rounded-3xl bg-white/70 backdrop-blur">
                <div class="inline-flex items-center gap-2 px-3 py-1 mx-auto mb-3 text-xs bg-white border rounded-full text-slate-900">
                    <x-icons.sparkles />
                    {{-- TODO: Replace with actual featured collection or new arrivals functionality --}}
                    New fall collection just dropped
                </div>
                <h1 class="text-4xl font-black tracking-tight md:text-5xl">
                    Clothing that carries <span class="underline decoration-emerald-300 decoration-4 underline-offset-4">good news</span>
                </h1>
                <p class="mt-3 text-slate-900 md:text-lg">
                    Thoughtfully designed, soft‑to‑live‑in apparel that whispers the Gospel in everyday moments.
                </p>
                <div class="flex items-center justify-center gap-3 mt-6">
                    <a href="#catalog" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium transition text-slate-900 rounded-2xl bg-sky-100 hover:bg-sky-100">
                        Shop collection
                    </a>
                    <a href="#about" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-medium transition bg-white border rounded-2xl border-sky-300 text-slate-900 hover:bg-blue-50">
                        Learn more
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="bg-white border-b">
        <div class="flex flex-col items-stretch gap-3 px-4 py-4 mx-auto max-w-7xl md:flex-row md:items-center md:justify-between md:px-6">
            <!-- Category Tabs -->
            <div class="flex flex-wrap w-full gap-2">
                @foreach ($this->categories as $cat)
                    <button
                        wire:click="$set('category', @js($cat))"
                        class="rounded-full border px-4 py-2 text-sm transition {{ $category === $cat ? 'border-sky-400 bg-sky-100 text-slate-900' : 'bg-white text-slate-900 hover:border-sky-300' }}"
                    >
                        {{ $cat }}
                    </button>
                @endforeach
            </div>

            <div class="flex items-center gap-2 md:flex-shrink-0">
                <!-- Search -->
                <div class="relative flex-1 md:w-64">
                    <x-icons.search class="absolute -translate-y-1/2 left-3 top-1/2 text-slate-900" />
                    <input
                        type="text"
                        wire:model.debounce.500ms="query"
                        placeholder="Search products"
                        class="w-full py-2 pr-4 text-sm border rounded-full border-sky-300 pl-9 focus:border-sky-400 focus:ring-1 focus:ring-sky-400"
                    />
                </div>

                <!-- Sort Dropdown -->
                <select
                    wire:model="sort"
                    class="px-4 py-2 text-sm border rounded-full border-sky-300 focus:border-sky-400 focus:ring-1 focus:ring-sky-400"
                >
                    <option value="featured">Featured</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                </select>
            </div>
        </div>
    </section>

    <!-- Product Catalog -->
    <section id="catalog" class="px-4 py-10 mx-auto max-w-7xl md:px-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($this->products as $product)
                <div class="overflow-hidden bg-white border-0 shadow-sm group rounded-3xl ring-1 ring-sky-100">
                    <!-- Product Image -->
                    <div class="relative p-0">
                        <a href="{{ route('product.view', $product->defaultUrl->slug) }}">
                            @if ($product->thumbnail)
                                <img
                                    src="{{ $product->thumbnail->getUrl('large') }}"
                                    alt="{{ $product->translateAttribute('name') }}"
                                    class="object-cover w-full h-64 transition-transform duration-500 group-hover:scale-105"
                                />
                            @else
                                <div class="flex items-center justify-center w-full h-64 bg-sky-100">
                                    <svg class="w-16 h-16 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </a>
                        @if ($this->saleCollection && $product->collections->contains($this->saleCollection))
                            <div class="absolute left-3 top-3">
                                <span class="px-3 py-1 text-xs font-medium rounded-full text-slate-900 bg-sky-100">sale</span>
                            </div>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="p-5">
                        <h3 class="text-lg font-semibold">
                            <a href="{{ route('product.view', $product->defaultUrl->slug) }}" class="hover:text-slate-900">
                                {{ $product->translateAttribute('name') }}
                            </a>
                        </h3>
                        <p class="mt-1 text-sm text-slate-900 line-clamp-2">
                            {!! $product->translateAttribute('description') ?? 'Premium quality apparel' !!}
                        </p>

                        <!-- Price -->
                        <div class="mt-3 text-lg font-bold">
                            @if ($product->variants->first())
                                {{ $product->variants->first()->basePrices->first()?->price->formatted() ?? '$0.00' }}
                            @endif
                        </div>
                    </div>

                    <!-- Product Footer -->
                    <div class="flex items-center justify-between p-5 pt-0">
                        <!-- Rating Stars -->
                        {{-- TODO: Implement actual product ratings system --}}
                        <div class="flex items-center gap-1 text-amber-500">
                            @for ($i = 0; $i < 4; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            @endfor
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>

                        <!-- View Button -->
                        <a
                            href="{{ route('product.view', $product->defaultUrl->slug) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium transition rounded-full text-slate-900 bg-sky-100 hover:bg-sky-100"
                        >
                            View
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center col-span-full">
                    <p class="text-slate-900">No products found matching your criteria.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Value Props -->
    <section class="bg-white border-y">
        <div class="grid grid-cols-1 gap-6 px-4 py-10 mx-auto max-w-7xl md:grid-cols-3 md:px-6">
            <div class="p-6 border shadow-sm rounded-3xl">
                <div class="text-lg font-bold">Softstyle & Premium Blanks</div>
                <div class="mt-1 text-slate-900">We source cozy, durable garments you'll reach for daily.</div>
            </div>
            <div class="p-6 border shadow-sm rounded-3xl">
                <div class="text-lg font-bold">DTF + Embroidery Craft</div>
                <div class="mt-1 text-slate-900">Crisp prints and stitched details that last with care.</div>
            </div>
            <div class="p-6 border shadow-sm rounded-3xl">
                <div class="text-lg font-bold">Small‑Batch, Heart‑Led</div>
                <div class="mt-1 text-slate-900">Designed near High Point & Asheboro, NC.</div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section id="about" class="overflow-hidden bg-gradient-to-r from-emerald-50 to-sky-50">
        <div class="px-4 mx-auto max-w-7xl py-14 md:px-6">
            <div class="grid items-center gap-8 p-4 shadow-xl rounded-3xl bg-white/70 sm:p-8 backdrop-blur md:grid-cols-2">
                <div>
                    <h3 class="text-2xl font-black tracking-tight">Stay in the loop</h3>
                    <p class="mt-1 text-slate-900">Drops, restocks, and pop‑up markets—straight to your inbox.</p>
                    {{-- TODO: Implement newsletter subscription functionality --}}
                    <form
                        wire:submit.prevent="$emit('newsletter-subscribe')"
                        class="flex flex-col items-stretch gap-2 mt-4 sm:flex-row sm:items-center"
                    >
                        <input
                            type="email"
                            placeholder="you@email.com"
                            class="flex-1 px-4 py-2 border rounded-2xl border-sky-300 focus:border-sky-400 focus:ring-1 focus:ring-sky-400"
                        />
                        <button
                            type="submit"
                            class="px-6 py-2 text-sm font-medium transition text-slate-900 rounded-2xl bg-sky-100 hover:bg-sky-100 whitespace-nowrap"
                        >
                            Subscribe
                        </button>
                    </form>
                    <p class="mt-2 text-xs text-slate-900">We respect your privacy. Unsubscribe anytime.</p>
                </div>
                <div class="p-4 bg-white border rounded-2xl sm:p-6">
                    <div class="text-sm font-semibold text-slate-700">About Woven in Agape</div>
                    <p class="mt-2 text-sm text-slate-900">
                        We're a small, family‑run Christian apparel shop crafting garments that carry gentle truth—
                        pieces that feel good, look good, and point to the One who is good.
                    </p>
                </div>
            </div>
        </div>
    </section>
    </div>
</div>
