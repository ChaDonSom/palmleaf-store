@props([
    'error' => false,
    'secondary' => false,
])
<input
    {{ $attributes->merge([
            'type' => 'text',
            'class' => 'w-full p-3 border border-gray-200 rounded-3xl sm:text-sm',
        ])->class([
            'border-red-400' => !!$error,
            'focus:ring-sky-200 focus:border-sky-200' => !$secondary,
            'focus:ring-yellow-600 focus:border-yellow-600' => $secondary,
        ]) }}
    maxlength="255"
>
