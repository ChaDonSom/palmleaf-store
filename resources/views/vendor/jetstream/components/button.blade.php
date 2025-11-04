<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-slate-800 border border-transparent rounded-3xl font-semibold text-xs text-white tracking-widest hover:bg-slate-700 active:bg-slate-900 focus:outline-none focus:border-slate-600 focus:ring focus:ring-slate-600 disabled:opacity-25 transition']) }}
>
    {{ $slot }}
</button>