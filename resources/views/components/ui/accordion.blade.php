<div
    x-data="accordion(@js($single))"
    {{ $attributes->merge(['class' => 'flex w-full flex-col gap-3']) }}
>
    {{ $slot }}
</div>