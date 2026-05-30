@extends('layouts.root-layout')

@section('body')
	<div class="absolute top-12 left-24 right-24 h-2 bg-linear-to-r from-fuchsia-500 to-cyan-500"></div>
	@include('components.header')
	<x-breadcrumbs />
	<main class="container mx-auto grow flex items-center justify-center">
		@yield('content')
	</main>
	@include('components.footer')
@endsection
