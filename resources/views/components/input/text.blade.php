@props([
    'error' => false,
])
<input
    {{ $attributes->merge([
            'type' => 'text',
            'class' => 'w-full p-3 border border-gray-200 rounded-3xl sm:text-sm focus:ring-yellow-600 focus:border-yellow-600',
        ])->class([
            'border-red-400' => !!$error,
        ]) }}
    maxlength="255"
>
