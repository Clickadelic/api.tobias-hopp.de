@extends('layouts.full-width-layout')

@section('content')
    <div class="border-2 border-primary shadow backdrop-blur mx-auto rounded max-w-lg">
        <div class="flex flex-col justify-center items-center bg-gray-900 px-12 py-6 md:py-12 rounded">                       
            <h2 class="flex gap-2 mb-3 font-medium text-gray-300 text-2xl"><span class="font-la-belle-aurore text-primary text-2xl">Toby's</span><span class="text-primary">{ </span><span>API-Service</span><span class="text-primary">}</span></h2>
            <p class="text-gray-400 text-sm text-center">This API provides access &shy;to various <br>features and functionalities &shy;of Toby's API-platform.</p>
            <div class="flex justify-center items-center gap-4 text-center">
                <code class="block mt-3 sm:mt-5 w-37.5 sm:w-55 md:w-88.75 text-gray-300 text-xs sm:text-sm md:text-base text-center"><a href="https://api.tobias-hopp.de/up" target="_blank" class="hover:underline">https://api.tobias-hopp.de/up</code>
                {{-- <button class="mt-4 text-gray-300 cursor-default hover:cursor-pointer" title="Copy to clipboard" id="copy-to-clipboard">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-plus" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7"></path>
                        <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"></path>
                        <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"></path>
                    </svg>
                </button> --}}
            </div>
        </div>
    </div>
@endsection