@extends('layouts.root')

@section('body')
    <main class="flex flex-col justify-center items-center grow">
        @yield('content')
    </main>
    @include('components.footer-scripts')
@endsection