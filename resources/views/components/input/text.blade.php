@props([
    'error' => false,
    'secondary' => false,
])
<input
    {{ $attributes->merge([
            'type' => 'text',
            'class' => 'w-full p-3 border border-gray-200 dark:border-gray-600 rounded-3xl sm:text-sm dark:bg-gray-700 dark:text-gray-100',
        ])->class([
            'border-red-400' => !!$error,
            'focus:ring-sky-200 focus:border-sky-200 dark:focus:ring-sky-600 dark:focus:border-sky-600' => !$secondary,
            'focus:ring-yellow-600 focus:border-yellow-600' => $secondary,
        ]) }}
    maxlength="255"
>
