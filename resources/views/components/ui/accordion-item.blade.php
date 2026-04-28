@props([
    'title',
    'index',
])

<div class="group">
    <button
        type="button"
        @click="toggle({{ $index }})"
        class="flex w-full items-center justify-between p-3 text-left text-gray-300 font-medium bg-gray-900 rounded-lg hover:cursor-pointer hover:bg-gray-800"
    >
        <span>{{ $title }}</span>

        <!-- simple icon -->
        <span
            class="transition-transform duration-200"
            :class="isOpen({{ $index }}) ? 'rotate-180' : ''"
        >
            ▼
        </span>
    </button>

    <div
        x-show="isOpen({{ $index }})"
        x-collapse
        x-cloak
        class="px-4 pb-4 text-md text-gray-300"
    >
        {{ $slot }}
    </div>
</div>