<footer class="border-t dark:border-gray-700 bg-white dark:bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 py-10 md:px-6">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
            <div>
                <div class="text-xl font-extrabold dark:text-white">{{ config('app.name') }}</div>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                    Faith‑forward apparel based near High Point & Asheboro, NC.
                </p>
                <div class="mt-4 flex items-center gap-3">
                    <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 dark:border-gray-600 hover:bg-slate-50 dark:hover:bg-gray-700 transition">
                        <x-icons.instagram />
                    </a>
                    <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 dark:border-gray-600 hover:bg-slate-50 dark:hover:bg-gray-700 transition">
                        <x-icons.facebook />
                    </a>
                    <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 dark:border-gray-600 hover:bg-slate-50 dark:hover:bg-gray-700 transition">
                        <x-icons.twitter />
                    </a>
                </div>
            </div>
            <div>
                <div class="font-semibold dark:text-white">Shop</div>
                <ul class="mt-3 space-y-2 text-sm text-slate-600 dark:text-slate-400">
                    <li><a href="{{ url('/') }}" class="hover:text-slate-900 dark:hover:text-slate-200">All Products</a></li>
                    @foreach($footerCollections as $collection)
                        <li>
                            <a href="{{ route('collection.view', $collection->defaultUrl->slug ?? '#') }}" class="hover:text-slate-900 dark:hover:text-slate-200">
                                {{ $collection->translateAttribute('name') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <div class="font-semibold dark:text-white">Company</div>
                <ul class="mt-3 space-y-2 text-sm text-slate-600 dark:text-slate-400">
                    <li><a href="{{ route('about.view') }}" class="hover:text-slate-900 dark:hover:text-slate-200">About</a></li>
                    <li><a href="{{ route('contact.view') }}" class="hover:text-slate-900 dark:hover:text-slate-200">Contact</a></li>
                </ul>
            </div>
            <div>
                <div class="font-semibold dark:text-white">Support</div>
                <ul class="mt-3 space-y-2 text-sm text-slate-600 dark:text-slate-400">
                    <li><a href="{{ route('shipping-returns.view') }}" class="hover:text-slate-900 dark:hover:text-slate-200">Shipping & Returns</a></li>
                    <li><a href="{{ route('size-guide.view') }}" class="hover:text-slate-900 dark:hover:text-slate-200">Size Guide</a></li>
                    <li><a href="{{ route('faq.view') }}" class="hover:text-slate-900 dark:hover:text-slate-200">FAQ</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t dark:border-gray-700 pt-6 text-xs text-slate-500 dark:text-slate-400 md:flex-row">
            <div>© {{ now()->year }} {{ config('app.name') }}. All rights reserved.</div>
            <div class="flex items-center gap-3">
                <a href="{{ route('terms.show') }}" class="hover:text-slate-900 dark:hover:text-slate-200">Terms</a>
                <a href="{{ route('policy.show') }}" class="hover:text-slate-900 dark:hover:text-slate-200">Privacy</a>
                <a href="{{ route('cookies.view') }}" class="hover:text-slate-900 dark:hover:text-slate-200">Cookies</a>
            </div>
        </div>
    </div>
</footer>
