<header class="bg-neutral-900 shadow-lg backdrop-blur border-primary border-b-2 w-full backdrop">
    <div class="flex justify-between items-center mx-auto p-4 md:py-4 container">
        <x-app-logo />
        <nav class="flex items-center justify-between gap-2">
			<x-mobile-menu />
            <ul class="gap-6 hidden md:flex">
                <li>
					<a href="/" class="font-medium hover:text-white hover:underline underline-offset-5 text-neutral-300 flex items-center justify-start gap-1" title="Start">
						<x-heroicon-o-home class="size-4 -mt-0.5" />
						Start
					</a>
				</li>
                <li>
					<a href="/docs" class="font-medium hover:text-white hover:underline underline-offset-5 text-neutral-300 flex items-center justify-start gap-1" title="Docs">
						<x-heroicon-o-document class="size-4 -mt-0.5" />
						Docs
					</a>
				</li>
                <li>
					<a href="/about" class="font-medium hover:text-white hover:underline underline-offset-5 text-neutral-300 flex items-center justify-start gap-1" title="Docs">
						<x-heroicon-o-question-mark-circle class="size-4 -mt-0.5" />
						About
					</a>
				</li>
            </ul>
        </nav>
    </div>
</header>