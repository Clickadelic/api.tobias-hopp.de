@extends('layouts.root')

@section('body')
    @include('components.header')
    @include('components.breadcrumbs')
    <main class="flex flex-col justify-center items-center grow">
        @yield('content')
    </main>
    @include('components.footer')
@endsection