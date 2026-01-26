@extends('dashboard')
@section('content')
    <div class="py-5">
        <div class="max-w-7xl mx-auto px-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Location --}}
            <div
                class="p-6 hover:bg-blue-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-gray-800 dark:border-gray-700 transition">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full mb-4">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg> --}}
                        <i class="bi bi-building text-blue-600 dark:text-blue-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Location</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Setup and manage different asset locations or departments.
                    </p>
                    <a href="{{ route('location.index') }}"
                        class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                        Open Location
                    </a>
                </div>
            </div>


        </div>
    </div>
@endsection
