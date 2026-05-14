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
        <nav class="flex">
			<x-mobile-menu />
            <ul class="gap-6 hidden md:flex">
                <li>
					<a href="/" class="font-medium text-neutral-300" title="Start">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
							<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
						</svg>
						Start
					</a>
				</li>
                <li><a href="/docs" class="font-medium text-neutral-300" title="Docs">Docs</a></li>
            </ul>
        </nav>
    </div>
</header>