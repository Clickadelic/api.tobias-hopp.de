<button
    {{ $attributes->merge([
        'class' => $classes(),
        'type' => $type,
    ]) }}
>
    {{ $slot }}
</button>
