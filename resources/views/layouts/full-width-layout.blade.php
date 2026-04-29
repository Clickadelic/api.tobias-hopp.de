@extends('layouts.root')

@section('body')
    @include('components.header')
    <x-breadcrumbs />
    <main class="container mx-auto grow">
        @yield('content')
    </main>
    @include('components.footer')
@endsection