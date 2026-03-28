@extends('layouts.full-width-layout')

@section('content')
    <div class="bg-white/30 backdrop-blur rounded p-2 shadow max-w-lg mx-auto">
        <div class="bg-white rounded py-6 md:py-12 px-12 flex flex-col items-center justify-center">                       
            <h2 class="text-2xl font-medium text-slate-800 mb-3 flex gap-2"><span class="font-la-belle-aurore">Toby's</span><span class="text-slate-500">{ </span><span>API</span><span class="text-slate-500"> }</span> Service</h2>
            <p class="text-sm text-slate-500">This API provides access &shy;to various <br>features and functionalities &shy;of Toby's platform.</p>
            <div class="flex items-center justify-center gap-4 ">
                <code class="mt-3 sm:mt-5 block text-center text-xs sm:text-sm md:text-base text-slate-600 w-37.5 sm:w-55 md:w-88.75">https://api.tobias-hopp.de/up</code>
                <button class="mt-4 cursor-default hover:cursor-pointer hover:text-slate-500" title="Copy to clipboard" id="copy-to-clipboard">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-plus" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7"></path>
                        <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"></path>
                        <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endsection