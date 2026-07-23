@extends('layouts.sidebar-left-layout')

@section('content')
<div class="w-full flex flex-col gap-4">
	<h3 class="font-medium text-2xl text-gray-200">Terms of privacy</h3>
	<div class="space-y-6">
		<section>
			<h3>
				All information according to §5 of the German Telemedia Act (Telemediengesetz, TMG):
			</h3>
			<ul class="list-inside my-6">
				<li>Tobias Hopp</li>
				<li>Oberer Markenweg 70</li>
				<li>56566 Neuwied</li>
				<li>Germany</li>
				<li>Contact: <a href="mailto:mail@tobias-hopp.de" class="hover:underline"
						titlte="E-Mail">mail@tobias-hopp.de</a></li>
			</ul>
			<p>
				This API is provided as a personal service for experimentation, development, and limited use.
				It is not offered as a guaranteed commercial product or enterprise-grade service.
			</p>
		</section>
		<section>
			<h3 class="font-medium text-xl text-gray-200 mb-2">Terms of Use</h3>
			<p>
				This API is provided on an “as is” and “as available” basis, without warranties of any kind,
				whether express or implied. I do not guarantee that the API will be uninterrupted, secure,
				error-free, accurate, or suitable for any particular purpose.
			</p>
	</div>
</div>
@endsection
