@extends('layouts.full-width-layout')

@section('content')
<div class="w-full flex flex-col gap-4">
	<h3 class="font-medium text-2xl text-gray-200">Docs</h3>
	<h4 class="font-medium text-xl text-gray-200">https://api.tobias-hopp.de/api/*</h4>
	<x-ui.accordion :single="true" class="w-full mx-auto">

		<x-ui.accordion-item title="Authentication" :index="1">
			<p class="text-gray-300">Obtain a Bearer token via register or login. Use the token in the
					<code>Authorization</code> header for protected routes.</p>
		</x-ui.accordion-item>

		<x-ui.accordion-item title="Item 2" :index="2">
			Content 2
		</x-ui.accordion-item>

		<x-ui.accordion-item title="Item 3" :index="3">
			Content 3
		</x-ui.accordion-item>

	</x-ui.accordion>
</div>
    
@endsection
