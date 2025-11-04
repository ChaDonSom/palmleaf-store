<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-3xl font-semibold text-xs text-gray-700 tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-slate-300 focus:ring focus:ring-slate-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition']) }}>
    {{ $slot }}
</button>
