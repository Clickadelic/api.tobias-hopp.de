@extends('layouts.frontpage-layout')

@section('content')
    <div class="min-h-full flex flex-col justify-center items-center gap-6">
        <div class="max-w-lg w-full border-2 border-primary rounded px-12 py-6 md:py-12">
            <div class="flex flex-col justify-center items-center bg-gray-900 px-12 py-6 md:py-12 rounded">
                <h2 class="flex gap-2 mb-3 font-medium text-gray-300 text-2xl"><span
                        class="font-la-belle-aurore text-primary text-2xl">Toby's</span><span class="text-primary">{
                    </span><span>API-Service</span><span class="text-primary">}</span></h2>
                <p class="text-gray-400 text-sm text-center">This API provides access &shy;to various <br>features and
                    functionalities &shy;of Toby's API-platform.</p>
                <div class="flex justify-center items-center gap-4 text-center">
                    <code
                        class="block mt-3 sm:mt-5 w-37.5 sm:w-55 md:w-88.75 text-gray-300 text-xs sm:text-sm md:text-base text-center">
                        <a href="https://api.tobias-hopp.de/up" target="_blank"
                            class="hover:underline">https://api.tobias-hopp.de/up</a>
                    </code>
                </div>
            </div>
        </div>
    </div>
@endsection
