@props([
    'error' => false,
])
<input
    {{ $attributes->merge([
            'type' => 'text',
            'class' => 'w-full p-3 border border-gray-200 rounded-3xl sm:text-sm focus:ring-green-600 focus:border-green-600',
        ])->class([
            'border-red-400' => !!$error,
        ]) }}
    maxlength="255"
>
