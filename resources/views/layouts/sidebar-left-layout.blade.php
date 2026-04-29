@extends('layouts.root')

@section('body')
    @include('components.header')
    <x-breadcrumbs />
    <main class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-6 py-8">
        <aside>
            @include('layouts.sidebars.left-sidebar')
        </aside>
        <section class="col-span-1 md:col-span-3 bg-gray-800 rounded-lg p-6">
            @yield('content')
        </section>
    </main>
    @include('components.footer')
@endsection
