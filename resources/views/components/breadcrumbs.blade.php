<div class="container mx-auto py-4 mb-4">
    <ul class="flex items-center text-gray-400 text-sm space-x-2">
		
        {{-- Home --}}
        <li class="flex items-center space-x-1">
            <x-heroicon-o-home class="w-4 h-4" />
            <a href="{{ route('home') }}" class="hover:text-white">
                Home
            </a>
        </li>

        {{-- Dynamic Breadcrumbs --}}
        @foreach ($items as $item)
            <li class="flex items-center space-x-2">
                <span>/</span>

                <a href="{{ $item['url'] }}"
                   class="capitalize hover:text-white">
                    {{ $item['name'] }}
                </a>
            </li>
        @endforeach

    </ul>
</div>