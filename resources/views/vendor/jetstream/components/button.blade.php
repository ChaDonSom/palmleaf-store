<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-3xl font-semibold text-xs text-white tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-600 focus:ring focus:ring-yellow-600 disabled:opacity-25 transition']) }}
>
    {{ $slot }}
</button>