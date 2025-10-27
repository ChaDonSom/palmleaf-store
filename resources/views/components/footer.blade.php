<footer class="border-t bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 md:px-6">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
            <div>
                <div class="text-xl font-extrabold">{{ config('app.name') }}</div>
                <p class="mt-2 text-sm text-slate-600">
                    Faith‑forward apparel based near High Point & Asheboro, NC.
                </p>
                <div class="mt-4 flex items-center gap-3">
                    <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 hover:bg-slate-50 transition">
                        <x-icons.instagram />
                    </a>
                    <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 hover:bg-slate-50 transition">
                        <x-icons.facebook />
                    </a>
                    <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 hover:bg-slate-50 transition">
                        <x-icons.twitter />
                    </a>
                </div>
            </div>
            <div>
                <div class="font-semibold">Shop</div>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li><a href="{{ url('/') }}" class="hover:text-slate-900">All Products</a></li>
                    @foreach($footerCollections as $collection)
                        <li>
                            <a href="{{ route('collection.view', $collection->defaultUrl->slug ?? '#') }}" class="hover:text-slate-900">
                                {{ $collection->translateAttribute('name') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <div class="font-semibold">Company</div>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li><a href="#about" class="hover:text-slate-900">About</a></li>
                    <li><a href="#" class="hover:text-slate-900">Contact</a></li>
                    <li><a href="#" class="hover:text-slate-900">Wholesale</a></li>
                </ul>
            </div>
            <div>
                <div class="font-semibold">Support</div>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li><a href="#" class="hover:text-slate-900">Shipping & Returns</a></li>
                    <li><a href="#" class="hover:text-slate-900">Size Guide</a></li>
                    <li><a href="#" class="hover:text-slate-900">FAQ</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t pt-6 text-xs text-slate-500 md:flex-row">
            <div>© {{ now()->year }} {{ config('app.name') }}. All rights reserved.</div>
            <div class="flex items-center gap-3">
                <a href="#" class="hover:text-slate-900">Terms</a>
                <a href="#" class="hover:text-slate-900">Privacy</a>
                <a href="#" class="hover:text-slate-900">Cookies</a>
            </div>
        </div>
    </div>
</footer>
