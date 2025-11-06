<x-layouts.base bodyClasses="font-sans antialiased">
    @push('fonts')
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @endpush

    @push('scripts')
        <script defer src="https://cdn.jsdelivr.net/npm/luxon@3.0.4/build/global/luxon.min.js"></script>
    @endpush

    @section('navigation')
        @livewire('components.navigation')
    @endsection

    {{ $slot }}

    @section('footer')
        <x-footer />
    @endsection
</x-layouts.base>
