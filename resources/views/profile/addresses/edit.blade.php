<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <livewire:profile.update-address-form :address="$address" />
        </div>
    </div>
</x-app-layout>
