@props(['title', 'index'])

<div class="group w-full">
    <button type="button" @click="toggle({{ $index }})" :aria-expanded="isOpen({{ $index }})"
        aria-controls="accordion-panel-{{ $index }}"
        class="flex w-full items-center justify-between rounded-lg bg-gray-900 p-3 text-left font-medium text-gray-300 hover:cursor-pointer hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-sky-500">
        <span>{{ $title }}</span>

        <!-- simple icon -->
        <span class="transition-transform duration-200" :class="isOpen({{ $index }}) ? 'rotate-180' : ''">
            ▼
        </span>
    </button>

    <div id="accordion-panel-{{ $index }}" x-show="isOpen({{ $index }})" x-collapse x-cloak
        class="px-4 pt-4 text-md text-gray-300">
        {{ $slot }}
    </div>
</div>
