<div class="container mx-auto py-4">
	<ul class="flex text-gray-300 text-sm space-x-2">
		<li>
			<a href="{{route('home')}}">Home</a>
		</li>
		@for($i = 0; $i <= count(Request::segments()); $i++)
			<li>
				<a href="{{implode('/', array_slice(Request::segments(), 0, $i))}}" class="capitalize">{{Request::segment($i)}}</a>
				@if($i < count(Request::segments()) & $i > 0)
					{!!'<i class="fa fa-angle-right"></i>'!!}
				@endif
			</li>
		@endfor
	</ul>
</div>