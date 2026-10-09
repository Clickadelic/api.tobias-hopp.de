@extends('layouts.sidebar-left-layout')

@section('content')
    <div class="mx-auto flex w-full max-w-3xl flex-col">
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-semibold text-white">About Toby's API</h1>
            <p class="text-neutral-200">
                This page demonstrates the reusable Blade accordion component.
                In single mode, opening one section closes the others.
            </p>
        </div>

        <x-ui.accordion single>
            <x-ui.accordion-item title="What is this API?" index="0">
                This Laravel application provides the backend services used by Toby's
                projects, including authentication, recipes, and shared application data.
            </x-ui.accordion-item>

            <x-ui.accordion-item title="How does the accordion work?" index="1">
                The Blade components provide the markup, while Alpine.js manages the
                open state and the collapse plugin animates the content.
            </x-ui.accordion-item>

            <x-ui.accordion-item title="Can multiple sections stay open?" index="2">
                Yes. Omit the <code>single</code> attribute on the accordion component
                to allow multiple items to remain open at the same time.
            </x-ui.accordion-item>
        </x-ui.accordion>
        <hr class="my-6 border-gray-700" />
        <h3 class="text-2xl mb-6">Buttons</h3>
        <div class="flex gap-3">
            <x-button variant="default" size="md" aria-label="Conversion Button">
                Default
            </x-button>
            <x-button variant="primary" size="md" aria-label="Conversion Button">
                Primary
            </x-button>
            <x-button variant="secondary" size="md" aria-label="Conversion Button">
                Secondary
            </x-button>
            <x-button variant="success" size="md" aria-label="Conversion Button">
                Success
            </x-button>
            <x-button variant="danger" size="md" aria-label="Conversion Button">
                Danger
            </x-button>
            <x-button variant="warning" size="md" aria-label="Conversion Button">
                Warning
            </x-button>
            <x-button variant="outline" size="md" aria-label="Conversion Button">
                Outline
            </x-button>
            <x-button variant="ghost" size="md" aria-label="Conversion Button">
                Ghost
            </x-button>
            <x-button variant="link" size="md" aria-label="Conversion Button">
                Link
            </x-button>
        </div>
    </div>
@endsection
