<div class="min-h-[60vh] flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div>
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md m-auto px-6 py-4 bg-transparent sm:bg-white shadow-none sm:shadow-md overflow-hidden sm:rounded-xl">
        {{ $slot }}
    </div>
</div>
