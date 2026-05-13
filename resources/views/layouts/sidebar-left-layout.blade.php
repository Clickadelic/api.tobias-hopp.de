@extends('layouts.root-layout')

@section('body')
    @include('components.header')
    <x-breadcrumbs />
    <main class="container grow mx-auto grid grid-cols-1 md:grid-cols-4 gap-4">
        <aside class="p-4 flex flex-col gap-4">
            @include('layouts.sidebars.left-sidebar')
        </aside>
        <section class="col-span-1 md:col-span-3 rounded-lg p-4 flex flex-col gap-4 justify-start">
            @yield('content')
        </section>
    </main>
    @include('components.footer')
@endsection
