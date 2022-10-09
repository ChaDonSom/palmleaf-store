<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-3xl font-semibold text-xs text-white tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-600 focus:ring focus:ring-green-600 disabled:opacity-25 transition']) }}
>
    {{ $slot }}
</button>