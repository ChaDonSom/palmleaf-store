@props([
    'secondary' => false,
])

<input type="checkbox" {!! $attributes->merge(['class' => 'shadow-sm transition border-gray-100 rounded-3xl'])->class([
    'focus:ring-yellow-600 bg-yellow-600 text-yellow-600' => $secondary,
    'focus:ring-sky-200 bg-sky-200 text-sky-300' => ! $secondary,
]) !!}>
