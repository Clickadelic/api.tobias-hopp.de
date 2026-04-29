@extends('layouts.root')

@section('body')
    <div class="min-h-full flex flex-col justify-center items-center gap-6">
        @include('components.header')
        <x-breadcrumbs />
        <main class="container mx-auto grow flex items-center justify-center">
            @yield('content')
        </main>
        @include('components.footer')
    </div>
@endsection
