<x-form-section submit="">
    <x-slot name="title">
        {{ __('Addresses') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Manage your account\'s addresses.') }}
    </x-slot>

    <x-slot name="form">
        <table class="col-span-full">
            <thead>
                <th class="dark:text-gray-300">{{ __('Address') }}</th>
                <th class="dark:text-gray-300">{{ __('Shipping Default') }}</th>
                <th class="dark:text-gray-300">{{ __('Billing Default') }}</th>
                <th></th>
            </thead>
            <tbody>
                @foreach ($addresses as $address)
                    <tr>
                        <td class="text-sm text-gray-600 dark:text-gray-400">{{ $address->line_one }}, {{ $address->city }}
                            {{ $address->state }}
                            {{ $address->postcode }}</td>
                        <td>
                            <div class="flex justify-center">
                                @if($address->shipping_default)
                                <input
                                    class="w-5 h-5 text-slate-900 border-gray-100 rounded-3xl"
                                    type="checkbox"
                                    disabled
                                    checked
                                />
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="flex justify-center">
                                @if($address->billing_default)
                                <input
                                    class="w-5 h-5 text-slate-900 border-gray-100 rounded-3xl"
                                    type="checkbox"
                                    disabled
                                    checked
                                />
                                @endif
                            </div>
                        </td>
                        <td>
                            <a type="button" href="{{ route('profile-edit-address', $address) }}">
                                <i
                                    class="w-9 h-9 material-icons text-[1.2rem] p-2 ml-auto text-gray-400 dark:text-gray-500 transition-colors rounded-3xl hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300"
                                >edit</i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td>
                        <a href="{{ route('profile-create-address') }}" class="inline-flex items-center px-4 py-2 my-2 bg-yellow-600 border border-transparent rounded-3xl font-semibold text-xs text-white tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring focus:ring-yellow-600 disabled:opacity-25 transition">
                            {{ __('Add address') }}
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </x-slot>
</x-form-section>
