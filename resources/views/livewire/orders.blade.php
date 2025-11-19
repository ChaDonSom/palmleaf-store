<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <x-form-section submit="">
        <x-slot name="title">{{ __('My orders') }}</x-slot>
        <x-slot name="description">{{ __('Here, you may view your order history, check order status, and make edits, if necessary.') }}</x-slot>
        <x-slot name="form">
            <table class="col-span-full">
                <thead>
                    <th class="text-left dark:text-gray-300">{{ __('Order reference') }}</th>
                    <th class="dark:text-gray-300">{{ __('Placed') }}</th>
                    <th class="text-right dark:text-gray-300">{{ __('Total') }}</th>
                    <th></th>
                </thead>
                <tbody>
                    @foreach ($this->orders as $order)
                        <tr>
                            <td class="py-1 dark:text-gray-300">{{ $order->reference }}</td>
                            <td
                                class="py-1 dark:text-gray-300"
                                x-data="{ date: '{{ $order->placed_at->toISOString() }}' }"
                                x-text="() => luxon.DateTime.fromISO(date).toLocaleString(
                                    {
                                        ...luxon.DateTime.DATETIME_SHORT,
                                        weekday: 'short'
                                    }
                                )"
                            ></td>
                            <td class="py-1 text-right dark:text-gray-300">{{ $order->total->formatted }}</td>
                            <td class="py-1 text-right">
                                <a href="{{ route('orders.show', ['order' => $order]) }}" class="underline text-sm text-gray-600 dark:text-gray-400 m-2">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="col-span-full">
                {{ $this->orders->links() }}
            </div>
        </x-slot>
    </x-form-section>
</div>