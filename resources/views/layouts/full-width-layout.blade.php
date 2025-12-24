@extends('layouts.root-layout')

@section('body')
    @include('components.header')
    <main class="flex-1">
        @yield('content')
    </main>
    @include('components.footer')
    @include('components.footer-scripts')
@endsection