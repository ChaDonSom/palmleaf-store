<div class="min-h-screen bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-16 md:py-24">
        <div class="rounded-3xl bg-white p-8 shadow-lg md:p-12">
            <h1 class="text-4xl font-black tracking-tight text-slate-900 md:text-5xl">
                Cookie Policy
            </h1>
            
            <div class="prose prose-slate mt-8 max-w-none">
                <p class="text-lg text-slate-600">
                    Last updated: {{ now()->format('F d, Y') }}
                </p>

                <div class="mt-8 space-y-6 text-slate-600">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">What Are Cookies?</h2>
                        <p class="mt-3">
                            Cookies are small text files that are stored on your device when you visit a website. They help us improve your browsing experience and provide certain features on our site.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">How We Use Cookies</h2>
                        <p class="mt-3">
                            We use cookies to:
                        </p>
                        <ul class="mt-3 list-inside list-disc space-y-1">
                            <li>Remember your preferences and settings</li>
                            <li>Keep you signed in to your account</li>
                            <li>Remember items in your shopping cart</li>
                            <li>Understand how you use our website</li>
                            <li>Improve our website's performance and functionality</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Types of Cookies We Use</h2>
                        
                        <div class="mt-4 space-y-4">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <h3 class="font-semibold text-slate-900">Essential Cookies</h3>
                                <p class="mt-2">These cookies are necessary for the website to function properly. They enable core functionality such as security, network management, and accessibility.</p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <h3 class="font-semibold text-slate-900">Functional Cookies</h3>
                                <p class="mt-2">These cookies allow us to remember your preferences and choices, such as your login details and language preferences.</p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <h3 class="font-semibold text-slate-900">Analytics Cookies</h3>
                                <p class="mt-2">These cookies help us understand how visitors interact with our website by collecting and reporting information anonymously.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Managing Cookies</h2>
                        <p class="mt-3">
                            You can control and/or delete cookies as you wish. Most web browsers allow you to manage your cookie preferences through the browser settings. Please note that blocking all cookies may impact your experience on our website and prevent you from using certain features.
                        </p>
                        <p class="mt-3">
                            To learn more about cookies and how to manage them, visit <a href="https://www.allaboutcookies.org" target="_blank" rel="noopener noreferrer" class="text-slate-900 underline hover:text-slate-700">www.allaboutcookies.org</a>.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Third-Party Cookies</h2>
                        <p class="mt-3">
                            Some cookies on our site are placed by third-party services that appear on our pages. We don't control these cookies and recommend reviewing the privacy policies of these third parties.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Changes to This Policy</h2>
                        <p class="mt-3">
                            We may update this Cookie Policy from time to time. Any changes will be posted on this page with an updated revision date.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-6">
                        <h2 class="text-2xl font-bold text-slate-900">Contact Us</h2>
                        <p class="mt-4">
                            If you have any questions about our use of cookies, please contact us at 
                            <a href="mailto:{{ config('mail.contact') }}" class="text-slate-900 underline hover:text-slate-700">{{ config('mail.contact') }}</a>
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
