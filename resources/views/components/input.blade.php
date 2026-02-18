@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-[#e91e63] focus:ring-[#e91e63] rounded-lg shadow-sm']) !!}>
