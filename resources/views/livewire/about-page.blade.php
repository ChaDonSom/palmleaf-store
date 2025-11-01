<div class="min-h-screen bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-16 md:py-24">
        <div class="rounded-3xl bg-white p-8 shadow-lg md:p-12">
            <h1 class="text-4xl font-black tracking-tight text-slate-900 md:text-5xl">
                About {{ config('app.name') }}
            </h1>
            
            <div class="prose prose-slate mt-8 max-w-none">
                <h2 class="text-2xl font-bold text-slate-900">Our Story</h2>
                <p class="text-slate-600">
                    {{ config('app.name') }} was founded with a simple mission: to create faith-forward apparel that carries the Gospel into everyday moments. Based near High Point & Asheboro, NC, we're passionate about designing clothing that's not only comfortable but meaningful.
                </p>

                <h2 class="mt-8 text-2xl font-bold text-slate-900">Our Values</h2>
                <p class="text-slate-600">
                    Every piece we create is thoughtfully designed to whisper the good news in subtle yet powerful ways. We believe that what we wear can be a conversation starter, a reminder of hope, and a declaration of faith.
                </p>

                <h2 class="mt-8 text-2xl font-bold text-slate-900">Quality & Craftsmanship</h2>
                <p class="text-slate-600">
                    We're committed to creating soft-to-live-in apparel that stands the test of time. Each item is carefully crafted with attention to detail, ensuring both comfort and durability.
                </p>

                <h2 class="mt-8 text-2xl font-bold text-slate-900">Join Our Community</h2>
                <p class="text-slate-600">
                    When you wear {{ config('app.name') }}, you're joining a community of believers who are passionate about living out their faith in everyday life. Thank you for being part of our story.
                </p>

                <div class="mt-8 flex items-center gap-4">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-sm font-medium text-white hover:bg-slate-800 transition">
                        Shop Collection
                    </a>
                    <a href="{{ route('contact.view') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-300 bg-white px-6 py-3 text-sm font-medium text-slate-900 hover:bg-slate-50 transition">
                        Get in Touch
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
