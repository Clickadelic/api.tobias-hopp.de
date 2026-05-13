@extends('layouts.root-layout')

@section('body')
	@include('components.header')
	<x-breadcrumbs />
	<main class="container mx-auto grow flex items-center justify-center">
		@yield('content')
	</main>
	@include('components.footer')
@endsection
