@extends('layouts.auth-layout')

@section('content')
    <div class="mx-auto flex w-full max-w-md flex-col items-center gap-6 rounded-lg border border-neutral-700 bg-neutral-800/50 p-8 text-center shadow-lg">
        <x-app-logo />

        <x-heroicon-o-check-circle class="size-16 text-emerald-500" />

        @if ($alreadyVerified)
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-semibold text-white">Already confirmed</h1>
                <p class="text-neutral-300">
                    Hi {{ $user->name }}, your email address <span class="font-medium text-white">{{ $user->email }}</span>
                    was already verified. You're all set.
                </p>
            </div>
        @else
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-semibold text-white">Email confirmed!</h1>
                <p class="text-neutral-300">
                    Thanks, {{ $user->name }}. Your email address <span class="font-medium text-white">{{ $user->email }}</span>
                    has been verified successfully.
                </p>
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-center gap-3">
            <a
                href="{{ config('app.frontend_url') }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2"
            >
                Continue
            </a>
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-neutral-500 px-4 py-2 text-sm font-medium text-neutral-100 shadow-sm transition-colors hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2"
            >
                Back to homepage
            </a>
        </div>
    </div>
@endsection
