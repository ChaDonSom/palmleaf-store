<div class="min-h-screen bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-16 md:py-24">
        <div class="rounded-3xl bg-white p-8 shadow-lg md:p-12">
            <h1 class="text-4xl font-black tracking-tight text-slate-900 md:text-5xl">
                Contact Us
            </h1>
            
            <div class="prose prose-slate mt-8 max-w-none">
                <p class="text-lg text-slate-600">
                    We'd love to hear from you! Whether you have questions about our products, need help with an order, or just want to say hello, feel free to reach out.
                </p>

                <div class="mt-8 space-y-6">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                        <h2 class="text-xl font-bold text-slate-900">Get in Touch</h2>
                        <div class="mt-4 space-y-3 text-slate-600">
                            <p>
                                <strong class="text-slate-900">Location:</strong><br />
                                Near High Point & Asheboro, NC
                            </p>
                            <p>
                                <strong class="text-slate-900">Email:</strong><br />
                                <a href="mailto:{{ config('mail.contact') }}" class="text-slate-900 underline hover:text-slate-700">{{ config('mail.contact') }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                        <h2 class="text-xl font-bold text-slate-900">Customer Service Hours</h2>
                        <div class="mt-4 text-slate-600">
                            <p>Monday - Friday: 9:00 AM - 5:00 PM EST</p>
                            <p class="mt-1">Saturday - Sunday: Closed</p>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                        <h2 class="text-xl font-bold text-slate-900">Follow Us</h2>
                        <p class="mt-4 text-slate-600">
                            Stay connected with us on social media for the latest updates, new arrivals, and inspiration.
                        </p>
                        <div class="mt-4 flex items-center gap-3">
                            <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 hover:bg-slate-100 transition">
                                <x-icons.instagram />
                            </a>
                            <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 hover:bg-slate-100 transition">
                                <x-icons.facebook />
                            </a>
                            <a href="#" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 hover:bg-slate-100 transition">
                                <x-icons.twitter />
                            </a>
                        </div>
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
