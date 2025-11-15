<x-layouts.base bodyClasses="">
    @section('navigation')
        @livewire('components.navigation')
    @endsection

    {{ $slot }}

    @section('footer')
        <x-footer />
    @endsection
</x-layouts.base>
