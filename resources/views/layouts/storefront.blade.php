<x-layouts.base>
    @push('fonts')
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @endpush

    @section('navigation')
        @livewire('components.navigation')
    @endsection

    {{ $slot }}

    @section('footer')
        <x-footer />
    @endsection
</x-layouts.base>
