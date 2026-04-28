@extends('layouts.full-width-layout')

@section('content')
<div class="w-full flex flex-col gap-4">
	<h3 class="font-medium text-2xl text-gray-200">Disclaimer</h3>
	<article>
		<p class="text-gray-300">This is a simple disclaimer.</p>
		<x-ui.accordion :single="true" class="w-full mx-auto">

		<x-ui.accordion-item title="Owner" :index="1">
			<p class="text-gray-300">Tobias Hopp</p>
		</x-ui.accordion-item>

		<x-ui.accordion-item title="Item 2" :index="2">
			Content 2
		</x-ui.accordion-item>

		<x-ui.accordion-item title="Item 3" :index="3">
			Content 3
		</x-ui.accordion-item>

	</x-ui.accordion>
	</article>
</div>
    
@endsection