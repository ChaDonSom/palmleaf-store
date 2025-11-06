<x-layouts.base>
    @section('title')
        Checkout - {{ $title ?? config('app.name') }}
    @endsection

    @push('head')
        @stripeScripts
        {{ $head }}
    @endpush

    @section('navigation')
        <header class="relative border-b border-gray-100">
            <div class="flex items-center h-16 px-4 mx-auto max-w-screen-2xl sm:px-6 lg:px-8">
                <a
                    class="flex items-center flex-shrink-0"
                    href="{{ url('/') }}"
                >
                    <span class="sr-only">Home</span>

                    <x-brand.logo class="text-indigo-600" />
                </a>
            </div>
        </header>
    @endsection

    {{ $slot }}

    @section('footer')
        <x-footer />
    @endsection
</x-layouts.base>
