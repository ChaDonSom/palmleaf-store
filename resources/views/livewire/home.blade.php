<div class="min-h-screen bg-gradient-to-b from-white to-slate-50 text-slate-800">
    <!-- Hero Section -->
    <section class="border-b bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1526318472351-c75fcf070305?q=80&w=1600&auto=format&fit=crop');">
        <div class="mx-auto max-w-7xl px-4 py-16 md:py-24">
            <div class="mx-auto max-w-2xl rounded-3xl bg-white/70 p-8 text-center shadow-xl backdrop-blur">
                <div class="mx-auto mb-3 inline-flex items-center gap-2 rounded-full border bg-white px-3 py-1 text-xs text-slate-600">
                    <x-icons.sparkles />
                    {{-- TODO: Replace with actual featured collection or new arrivals functionality --}}
                    New fall collection just dropped
                </div>
                <h1 class="text-4xl font-black tracking-tight md:text-5xl">
                    Clothing that carries <span class="underline decoration-emerald-300 decoration-4 underline-offset-4">good news</span>
                </h1>
                <p class="mt-3 text-slate-600 md:text-lg">
                    Thoughtfully designed, soft‑to‑live‑in apparel that whispers the Gospel in everyday moments.
                </p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <a href="#catalog" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-sm font-medium text-white hover:bg-slate-800 transition">
                        Shop collection
                    </a>
                    <a href="#about" class="inline-flex items-center gap-2 rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-medium text-slate-900 hover:bg-slate-50 transition">
                        Learn more
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="border-b bg-white">
        <div class="mx-auto flex max-w-7xl flex-col items-stretch gap-3 px-4 py-4 md:flex-row md:items-center md:justify-between md:px-6">
            <!-- Category Tabs -->
            <div class="flex flex-wrap gap-2 w-full">
                @foreach ($this->categories as $cat)
                    <button
                        wire:click="$set('category', @js($cat))"
                        class="rounded-full border px-4 py-2 text-sm transition {{ $category === $cat ? 'border-slate-900 bg-slate-900 text-white' : 'bg-white text-slate-900 hover:border-slate-400' }}"
                    >
                        {{ $cat }}
                    </button>
                @endforeach
            </div>

            <div class="flex items-center gap-2 md:flex-shrink-0">
                <!-- Search -->
                <div class="relative flex-1 md:w-64">
                    <x-icons.search class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        wire:model.debounce.500ms="query"
                        placeholder="Search products"
                        class="w-full rounded-full border border-slate-300 pl-9 pr-4 py-2 text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900"
                    />
                </div>

                <!-- Sort Dropdown -->
                <select
                    wire:model="sort"
                    class="rounded-full border border-slate-300 px-4 py-2 text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900"
                >
                    <option value="featured">Featured</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                </select>
            </div>
        </div>
    </section>

    <!-- Product Catalog -->
    <section id="catalog" class="mx-auto max-w-7xl px-4 py-10 md:px-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($this->products as $product)
                <div class="group overflow-hidden rounded-3xl border-0 shadow-sm ring-1 ring-slate-100 bg-white">
                    <!-- Product Image -->
                    <div class="relative p-0">
                        <a href="{{ route('product.view', $product->defaultUrl->slug) }}">
                            @if ($product->thumbnail)
                                <img
                                    src="{{ $product->thumbnail->getUrl('large') }}"
                                    alt="{{ $product->translateAttribute('name') }}"
                                    class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                            @else
                                <div class="h-64 w-full bg-slate-200 flex items-center justify-center">
                                    <svg class="h-16 w-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </a>
                        @if ($this->saleCollection && $product->collections->contains($this->saleCollection))
                            <div class="absolute left-3 top-3">
                                <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-medium text-white">sale</span>
                            </div>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="p-5">
                        <h3 class="text-lg font-semibold">
                            <a href="{{ route('product.view', $product->defaultUrl->slug) }}" class="hover:text-slate-600">
                                {{ $product->translateAttribute('name') }}
                            </a>
                        </h3>
                        <p class="mt-1 text-sm text-slate-500 line-clamp-2">
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
                                <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            @endfor
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>

                        <!-- View Button -->
                        <a
                            href="{{ route('product.view', $product->defaultUrl->slug) }}"
                            class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 transition"
                        >
                            View
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-slate-500">No products found matching your criteria.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Value Props -->
    <section class="border-y bg-white">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 py-10 md:grid-cols-3 md:px-6">
            <div class="rounded-3xl border p-6 shadow-sm">
                <div class="text-lg font-bold">Softstyle & Premium Blanks</div>
                <div class="mt-1 text-slate-600">We source cozy, durable garments you'll reach for daily.</div>
            </div>
            <div class="rounded-3xl border p-6 shadow-sm">
                <div class="text-lg font-bold">DTF + Embroidery Craft</div>
                <div class="mt-1 text-slate-600">Crisp prints and stitched details that last with care.</div>
            </div>
            <div class="rounded-3xl border p-6 shadow-sm">
                <div class="text-lg font-bold">Small‑Batch, Heart‑Led</div>
                <div class="mt-1 text-slate-600">Designed near High Point & Asheboro, NC.</div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section id="about" class="bg-gradient-to-r from-emerald-50 to-sky-50 overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 py-14 md:px-6">
            <div class="grid items-center gap-8 rounded-3xl bg-white/70 p-4 sm:p-8 shadow-xl backdrop-blur md:grid-cols-2">
                <div>
                    <h3 class="text-2xl font-black tracking-tight">Stay in the loop</h3>
                    <p class="mt-1 text-slate-600">Drops, restocks, and pop‑up markets—straight to your inbox.</p>
                    {{-- TODO: Implement newsletter subscription functionality --}}
                    <form
                        wire:submit.prevent="$emit('newsletter-subscribe')"
                        class="mt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-2"
                    >
                        <input
                            type="email"
                            placeholder="you@email.com"
                            class="flex-1 rounded-2xl border border-slate-300 px-4 py-2 focus:border-slate-900 focus:ring-1 focus:ring-slate-900"
                        />
                        <button
                            type="submit"
                            class="rounded-2xl bg-slate-900 px-6 py-2 text-sm font-medium text-white hover:bg-slate-800 transition whitespace-nowrap"
                        >
                            Subscribe
                        </button>
                    </form>
                    <p class="mt-2 text-xs text-slate-500">We respect your privacy. Unsubscribe anytime.</p>
                </div>
                <div class="rounded-2xl border bg-white p-4 sm:p-6">
                    <div class="text-sm font-semibold text-slate-700">About Woven in Agape</div>
                    <p class="mt-2 text-sm text-slate-600">
                        We're a small, family‑run Christian apparel shop crafting garments that carry gentle truth—
                        pieces that feel good, look good, and point to the One who is good.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
