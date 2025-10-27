<div class="min-h-screen bg-gradient-to-b from-white to-slate-50">
    <div class="mx-auto max-w-4xl px-4 py-16 md:py-24">
        <div class="rounded-3xl bg-white p-8 shadow-lg md:p-12">
            <h1 class="text-4xl font-black tracking-tight text-slate-900 md:text-5xl">
                Size Guide
            </h1>
            
            <div class="prose prose-slate mt-8 max-w-none">
                <p class="text-lg text-slate-600">
                    Find your perfect fit with our comprehensive size guide. All measurements are in inches.
                </p>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-2xl font-bold text-slate-900">Men's T-Shirts</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full border-collapse text-sm text-slate-600">
                            <thead>
                                <tr class="border-b border-slate-300">
                                    <th class="px-4 py-2 text-left font-semibold text-slate-900">Size</th>
                                    <th class="px-4 py-2 text-left font-semibold text-slate-900">Chest</th>
                                    <th class="px-4 py-2 text-left font-semibold text-slate-900">Length</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2">Small</td>
                                    <td class="px-4 py-2">34-36</td>
                                    <td class="px-4 py-2">27-28</td>
                                </tr>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2">Medium</td>
                                    <td class="px-4 py-2">38-40</td>
                                    <td class="px-4 py-2">28-29</td>
                                </tr>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2">Large</td>
                                    <td class="px-4 py-2">42-44</td>
                                    <td class="px-4 py-2">29-30</td>
                                </tr>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2">X-Large</td>
                                    <td class="px-4 py-2">46-48</td>
                                    <td class="px-4 py-2">30-31</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">2X-Large</td>
                                    <td class="px-4 py-2">50-52</td>
                                    <td class="px-4 py-2">31-32</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-2xl font-bold text-slate-900">Women's T-Shirts</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full border-collapse text-sm text-slate-600">
                            <thead>
                                <tr class="border-b border-slate-300">
                                    <th class="px-4 py-2 text-left font-semibold text-slate-900">Size</th>
                                    <th class="px-4 py-2 text-left font-semibold text-slate-900">Bust</th>
                                    <th class="px-4 py-2 text-left font-semibold text-slate-900">Length</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2">Small</td>
                                    <td class="px-4 py-2">32-34</td>
                                    <td class="px-4 py-2">25-26</td>
                                </tr>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2">Medium</td>
                                    <td class="px-4 py-2">35-37</td>
                                    <td class="px-4 py-2">26-27</td>
                                </tr>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2">Large</td>
                                    <td class="px-4 py-2">38-40</td>
                                    <td class="px-4 py-2">27-28</td>
                                </tr>
                                <tr class="border-b border-slate-200">
                                    <td class="px-4 py-2">X-Large</td>
                                    <td class="px-4 py-2">41-43</td>
                                    <td class="px-4 py-2">28-29</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">2X-Large</td>
                                    <td class="px-4 py-2">44-46</td>
                                    <td class="px-4 py-2">29-30</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <h2 class="text-2xl font-bold text-slate-900">How to Measure</h2>
                    <div class="mt-4 space-y-3 text-slate-600">
                        <div>
                            <h3 class="font-semibold text-slate-900">Chest/Bust</h3>
                            <p>Measure around the fullest part of your chest/bust, keeping the tape measure horizontal.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">Length</h3>
                            <p>Measure from the highest point of the shoulder down to the hem.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-emerald-100 bg-emerald-50 p-6">
                    <h2 class="text-2xl font-bold text-slate-900">Still Not Sure?</h2>
                    <p class="mt-4 text-slate-600">
                        If you're between sizes or have questions about fit, feel free to contact us at 
                        <a href="mailto:hello@{{ strtolower(str_replace(' ', '', config('app.name'))) }}.com" class="text-slate-900 underline hover:text-slate-700">
                            hello@{{ strtolower(str_replace(' ', '', config('app.name'))) }}.com
                        </a>
                        and we'll help you find the perfect size!
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
