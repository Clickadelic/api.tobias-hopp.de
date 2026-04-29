<div
    x-data="accordion(@js($single))"
    {{ $attributes->merge(['class' => 'asd']) }}
>
    {{ $slot }}
</div>