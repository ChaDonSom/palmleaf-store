<div class="min-h-screen bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-16 md:py-24">
        <div class="rounded-3xl bg-white p-8 shadow-lg md:p-12">
            <h1 class="text-4xl font-black tracking-tight text-slate-900 md:text-5xl">
                Shipping & Returns
            </h1>
            
            <div class="prose prose-slate mt-8 max-w-none">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-2xl font-bold text-slate-900">Shipping Information</h2>
                    <div class="mt-4 space-y-4 text-slate-600">
                        <div>
                            <h3 class="font-semibold text-slate-900">Processing Time</h3>
                            <p class="mt-1">Orders are typically processed within 2-3 business weeks (Monday-Friday, excluding holidays).</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Shipping Methods</h3>
                            <ul class="mt-1 list-inside list-disc space-y-1">
                                <li>Standard Shipping (5-7 business days)</li>
                                <li>Expedited Shipping (2-3 business days)</li>
                                <li>Express Shipping (1-2 business days)</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Domestic Shipping</h3>
                            <p class="mt-1">We ship throughout the United States. Free standard shipping on orders over $50.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">International Shipping</h3>
                            <p class="mt-1">Currently, we only ship within the United States. International shipping coming soon!</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-2xl font-bold text-slate-900">Return Policy</h2>
                    <div class="mt-4 space-y-4 text-slate-600">
                        <p class="text-lg">
                            We want you to love your {{ config('app.name') }} purchase! If you're not completely satisfied, we accept returns within 30 days of delivery.
                        </p>
                        <div>
                            <h3 class="font-semibold text-slate-900">Return Requirements</h3>
                            <ul class="mt-1 list-inside list-disc space-y-1">
                                <li>Items must be unworn, unwashed, and in original condition</li>
                                <li>Tags must still be attached</li>
                                <li>Original packaging is preferred but not required</li>
                                <li>Proof of purchase is required</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">How to Return</h3>
                            <ol class="mt-1 list-inside list-decimal space-y-1">
                                <li>Contact us at <a href="mailto:{{ config('mail.contact') }}" class="text-slate-900 underline hover:text-slate-700">{{ config('mail.contact') }}</a> to initiate a return</li>
                                <li>We'll provide you with a return authorization and instructions</li>
                                <li>Ship the item back using a trackable shipping method</li>
                                <li>Refunds are processed within 5-7 business days of receiving your return</li>
                            </ol>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Return Shipping</h3>
                            <p class="mt-1">Return shipping costs are the responsibility of the customer unless the item is defective or we made an error.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-2xl font-bold text-slate-900">Exchanges</h2>
                    <div class="mt-4 text-slate-600">
                        <p>Need a different size or color? We're happy to help! Contact us and we'll make the exchange process as smooth as possible.</p>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-emerald-100 bg-emerald-50 p-6">
                    <h2 class="text-2xl font-bold text-slate-900">Questions?</h2>
                    <p class="mt-4 text-slate-600">
                        If you have any questions about shipping or returns, please don't hesitate to contact us at 
                        <a href="mailto:{{ config('mail.contact') }}" class="text-slate-900 underline hover:text-slate-700">{{ config('mail.contact') }}</a>
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
