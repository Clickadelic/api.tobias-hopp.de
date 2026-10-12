@extends('layouts.auth-layout')

@section('content')
    <div class="mx-auto flex w-full max-w-md flex-col items-center gap-6 rounded-lg border border-neutral-700 bg-neutral-800/50 p-8 text-center shadow-lg">
        <x-app-logo />

        <x-heroicon-o-exclamation-triangle class="size-16 text-amber-500" />

        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-semibold text-white">Verification link invalid</h1>
            <p class="text-neutral-300">
                This confirmation link is invalid or has expired. Please log in and request a new
                verification email.
            </p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-600 focus:ring-offset-2"
            >
                Back to homepage
            </a>
        </div>
    </div>
@endsection
