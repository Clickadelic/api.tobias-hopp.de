@extends('layouts.frontpage-layout')

@section('content')

        <div class="max-w-lg w-full border-2 border-primary rounded m-4">
            <div class="flex flex-col justify-center items-center bg-gray-900 p-4 md:py-12 rounded">
                <h2 class="flex justify-center items-center gap-2 mb-3 font-medium  text-sm md:text-2xl bg-linear-to-r from-teal-400 to-yellow-200 bg-clip-text text-transparent"><span
                        class="font-la-belle-aurore text-primary text-2xl">Toby's</span><span class="text-primary">{
                    </span><span class="text-wrap">API-Service</span><span class="text-primary">}</span></h2>

                <p class="text-gray-300 text-sm text-center">This API provides access &shy;to various <br>features and
                    functionalities &shy;of Toby's API-platform.</p>
                <div class="flex justify-center items-center gap-4 text-center">
                    <code
                        class="w-full block mt-3 sm:mt-5 sm:w-55 md:w-88.75 text-gray-300 text-xs sm:text-sm text-center">
                        <a href="https://api.tobias-hopp.de/up" target="_blank"
                            class="hover:underline">https://api.tobias-hopp.de/up</a>
                    </code>
                </div>
				<div class="flex justify-center items-center gap-4 text-center">
					<a href="/docs" class="w-full block mt-3 sm:mt-5 sm:w-55 md:w-88.75 text-gray-300 text-xs sm:text-sm text-center hover:underline">Check the Docs</a>
				</div>
            </div>
        </div>

@endsection
