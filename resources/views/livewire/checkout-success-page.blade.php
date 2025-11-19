<section class="bg-white dark:bg-gray-900">
    <div class="max-w-screen-xl px-4 py-32 mx-auto sm:px-6 lg:px-8 lg:py-48">
        <div class="max-w-xl mx-auto text-center">
            @env('local')
            <span class="text-xs font-medium text-center bg-orange-100 text-orange-700 px-3 py-1.5 rounded-lg">
                This was a test order
            </span>
            @endenv

            <h1 class="mt-8 text-3xl font-extrabold sm:text-5xl">
                <span
                    class="block"
                    role="img"
                >
                    🥳
                </span>

                <span class="block mt-1 text-yellow-500">
                    Order Successful!
                </span>
            </h1>

            <p class="mt-4 font-medium sm:text-lg dark:text-gray-300">
                Your order reference number is

                <a href="{{ route('orders.show', $order->id) }}">
                    <strong>{{ $order->reference }}</strong>
                </a>
            </p>

            <a
                class="inline-block px-8 py-3 mt-8 text-sm font-medium text-center text-white bg-yellow-500 rounded-3xl hover:ring-1 hover:ring-yellow-600"
                href="{{ url('/') }}"
            >
                Back Home
            </a>
        </div>
    </div>
</section>
