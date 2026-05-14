<header class="bg-neutral-900 shadow-lg backdrop-blur border-primary border-b-2 w-full backdrop">
    <div class="flex justify-between items-center mx-auto p-4 md:py-4 container">
        <h1 className="app-logo">
            <a href="/" class="group flex flex-row justify-between gap-1 sm:gap-2">
                <span class="font-la-belle-aurore text-primary text-2xl">Toby's</span>
                <span class="inline-block font-medium text-neutral-300 text-xl leading-snug">
                    <span class="text-primary">{</span> API-Service <span class="text-primary">}</span>
                </span>
            </a>
        </h1>
        <nav class="flex items-center justify-between gap-2">
			<x-mobile-menu />
            <ul class="gap-6 hidden md:flex">
                <li>
					<a href="/" class="font-medium text-neutral-300 flex items-center justify-start gap-1" title="Start">
						<x-heroicon-o-home class="size-4 -mt-0.5" />
						Start
					</a>
				</li>
                <li>
					<a href="/docs" class="font-medium text-neutral-300 flex items-center justify-start gap-1" title="Docs">
						<x-heroicon-o-document class="size-4 -mt-0.5" />
						Docs
					</a>
				</li>
            </ul>
        </nav>
    </div>
</header>