@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-slate-300 focus:ring focus:ring-slate-200 focus:ring-opacity-50 rounded-md shadow-sm']) !!}>
