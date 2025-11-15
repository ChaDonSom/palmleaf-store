@props([
    'label' => null,
    'errors' => [],
    'required' => false,
])

<label {{ $attributes->merge(['class' => 'space-y-1']) }}>
    <div class="font-medium text-sm dark:text-gray-300">
        {{ $label }} @if ($required)<small class="text-xs text-red-500">&#42;</small>@endif
    </div>

    {{ $slot }}
    @foreach ($errors as $error)
        <p class="my-1 text-sm text-red-500">
            {{ $error }}
        </p>
    @endforeach
</label>
