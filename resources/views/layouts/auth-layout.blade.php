@extends('layouts.root-layout')

@section('body')

    <main class="flex-1">
        @yield('content')
    </main>
    @include('components.footer-scripts')
@endsection