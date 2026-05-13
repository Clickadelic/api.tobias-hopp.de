@extends('layouts.root-layout')

@section('body')
    @include('components.header')
    <x-breadcrumbs />
    <main class="container grow mx-auto grid grid-cols-1 md:grid-cols-4 gap-6">
        <aside class="asd">
            @include('layouts.sidebars.left-sidebar')
        </aside>
        <section class="col-span-1 md:col-span-3 asd rounded-lg p-6">
            @yield('content')
        </section>
    </main>
    @include('components.footer')
@endsection
