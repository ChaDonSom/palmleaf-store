<div class="min-h-screen bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-16 md:py-24">
        <div class="rounded-3xl bg-white p-8 shadow-lg md:p-12">
            <h1 class="text-4xl font-black tracking-tight text-slate-900 md:text-5xl">
                Frequently Asked Questions
            </h1>
            
            <div class="mt-8 space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-xl font-bold text-slate-900">Orders & Payment</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <h3 class="font-semibold text-slate-900">What payment methods do you accept?</h3>
                            <p class="mt-2 text-slate-600">We accept all major credit cards (Visa, Mastercard, American Express, Discover) and PayPal.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Can I modify or cancel my order?</h3>
                            <p class="mt-2 text-slate-600">Please contact us immediately if you need to modify or cancel your order. Once an order has been processed and shipped, we cannot make changes.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-xl font-bold text-slate-900">Shipping & Delivery</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <h3 class="font-semibold text-slate-900">How long will it take to receive my order?</h3>
                            <p class="mt-2 text-slate-600">Orders typically ship within 2-3 business weeks. Standard shipping takes 5-7 business days, while expedited options are available for faster delivery.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Do you ship internationally?</h3>
                            <p class="mt-2 text-slate-600">Currently, we only ship within the United States. If you're interested in international shipping, please let us know!</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">How can I track my order?</h3>
                            <p class="mt-2 text-slate-600">Once your order ships, you'll receive a tracking number via email. You can use this to track your package.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-xl font-bold text-slate-900">Returns & Exchanges</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <h3 class="font-semibold text-slate-900">What is your return policy?</h3>
                            <p class="mt-2 text-slate-600">We accept returns within 30 days of delivery. Items must be unworn, unwashed, and in original condition with tags attached. See our <a href="{{ route('shipping-returns.view') }}" class="text-slate-900 underline hover:text-slate-700">Shipping & Returns</a> page for full details.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">How do I exchange an item for a different size?</h3>
                            <p class="mt-2 text-slate-600">Contact us and we'll help you with the exchange process. We'll make it as smooth as possible!</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Who pays for return shipping?</h3>
                            <p class="mt-2 text-slate-600">Return shipping costs are the responsibility of the customer unless the item is defective or we made an error.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-xl font-bold text-slate-900">Products & Sizing</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <h3 class="font-semibold text-slate-900">How do I know what size to order?</h3>
                            <p class="mt-2 text-slate-600">Check out our detailed <a href="{{ route('size-guide.view') }}" class="text-slate-900 underline hover:text-slate-700">Size Guide</a> for measurements. If you're still unsure, feel free to contact us!</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Are your products pre-shrunk?</h3>
                            <p class="mt-2 text-slate-600">Yes, our apparel is pre-shrunk to minimize shrinkage. However, we always recommend following the care instructions on the label.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">What materials are your products made from?</h3>
                            <p class="mt-2 text-slate-600">We use high-quality, soft cotton and cotton blends for maximum comfort. Specific fabric details are listed on each product page.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-xl font-bold text-slate-900">Care Instructions</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <h3 class="font-semibold text-slate-900">How should I care for my {{ config('app.name') }} products?</h3>
                            <p class="mt-2 text-slate-600">Machine wash cold with like colors, tumble dry low, and avoid bleach. For best results, wash inside out to preserve any prints or designs.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-6">
                    <h2 class="text-xl font-bold text-slate-900">Still Have Questions?</h2>
                    <p class="mt-4 text-slate-600">
                        We're here to help! Contact us at 
                        <a href="mailto:{{ config('mail.contact') }}" class="text-slate-900 underline hover:text-slate-700">{{ config('mail.contact') }}</a>
                        or visit our <a href="{{ route('contact.view') }}" class="text-slate-900 underline hover:text-slate-700">Contact</a> page.
                    </p>
                </div>

                <div class="mt-8">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-sm font-medium text-white hover:bg-slate-800 transition">
                        Back to Shop
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
