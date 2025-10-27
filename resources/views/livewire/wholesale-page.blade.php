<div class="min-h-screen bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-16 md:py-24">
        <div class="rounded-3xl bg-white p-8 shadow-lg md:p-12">
            <h1 class="text-4xl font-black tracking-tight text-slate-900 md:text-5xl">
                Wholesale Inquiries
            </h1>
            
            <div class="prose prose-slate mt-8 max-w-none">
                <p class="text-lg text-slate-600">
                    Thank you for your interest in carrying {{ config('app.name') }} products in your store! We're excited to partner with retailers who share our values and mission.
                </p>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-2xl font-bold text-slate-900">Why Partner With Us?</h2>
                    <ul class="mt-4 space-y-2 text-slate-600">
                        <li class="flex items-start gap-2">
                            <span class="mt-1 text-emerald-500">✓</span>
                            <span>High-quality, faith-forward apparel that resonates with customers</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 text-emerald-500">✓</span>
                            <span>Competitive wholesale pricing with attractive margins</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 text-emerald-500">✓</span>
                            <span>Flexible minimum order quantities</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 text-emerald-500">✓</span>
                            <span>Marketing support and brand materials</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 text-emerald-500">✓</span>
                            <span>Reliable shipping and customer service</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-2xl font-bold text-slate-900">Wholesale Requirements</h2>
                    <div class="mt-4 text-slate-600">
                        <p>To qualify for our wholesale program, we require:</p>
                        <ul class="mt-3 list-inside list-disc space-y-1">
                            <li>Valid business license or resale certificate</li>
                            <li>Physical retail location or established online store</li>
                            <li>Alignment with our brand values and mission</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-emerald-100 bg-emerald-50 p-6">
                    <h2 class="text-2xl font-bold text-slate-900">Get Started</h2>
                    <p class="mt-4 text-slate-600">
                        Ready to bring {{ config('app.name') }} to your customers? Contact our wholesale team to learn more and receive our wholesale catalog.
                    </p>
                    <div class="mt-4">
                        <p class="text-slate-900">
                            <strong>Email:</strong> 
                            <a href="mailto:wholesale@{{ strtolower(str_replace(' ', '', config('app.name'))) }}.com" class="text-slate-900 underline hover:text-slate-700">
                                wholesale@{{ strtolower(str_replace(' ', '', config('app.name'))) }}.com
                            </a>
                        </p>
                    </div>
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
