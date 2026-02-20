@extends('dashboard')
@section('content')
    @php
        use Illuminate\Support\Facades\Auth;
    @endphp
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

            {{-- Category --}}
            <div
                class="p-6 hover:bg-yellow-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-gray-800 dark:border-gray-700 transition">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full mb-4">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-yellow-600 dark:text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg> --}}
                        <i class="bi bi-grid-3x3-gap text-yellow-600 dark:text-yellow-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Category</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Setup and manage different asset categories.
                    </p>
                    <a href="{{ route('category.index') }}"
                        class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                        Open Category
                    </a>
                </div>
            </div>

            {{-- License Type --}}
            <div
                class="p-6 hover:bg-green-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-gray-800 dark:border-gray-700 transition">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full mb-4">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg> --}}
                        <i class="bi bi-person-vcard text-green-600 dark:text-green-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">License Type</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Setup and manage different license types.
                    </p>
                    <a href="{{ route('license.index') }}"
                        class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                        Open License Type
                    </a>
                </div>
            </div>

            {{-- Employee ID Type --}}
            <div
                class="p-6 hover:bg-orange-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-gray-800 dark:border-gray-700 transition">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full mb-4">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-orange-600 dark:text-orange-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg> --}}
                        <i class="bi bi-credit-card-2-front text-orange-600 dark:text-orange-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Government ID Type</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Setup and manage different government id's.
                    </p>
                    <a href="{{ route('idtype.index') }}"
                        class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                        Open Government ID Type
                    </a>
                </div>
            </div>

            {{-- Supplies Category --}}
            <div
                class="p-6 hover:bg-indigo-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-gray-800 dark:border-gray-700 transition">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-full mb-4">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg> --}}
                        <i class="bi bi-box-seam text-indigo-600 dark:text-indigo-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Supplies Category</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Setup and manage different supplies categories.
                    </p>
                    <a href="{{ route('supplies-category.index') }}"
                        class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                        Open Category
                    </a>
                </div>
            </div>

            {{-- UOM --}}
            <div
                class="p-6 hover:bg-gray-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-gray-800 dark:border-gray-700 transition">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-gray-100 dark:bg-gray-900 rounded-full mb-4">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg> --}}
                        <i class="bi bi-rulers text-gray-600 dark:text-gray-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">UOM</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Setup and manage different units of measure.
                    </p>
                    <a href="{{ route('uom.index') }}"
                        class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                        Open UOM
                    </a>
                </div>
            </div>

            {{-- Supplier --}}
            <div
                class="p-6 hover:bg-purple-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-purple-800 dark:border-purple-700 transition">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full mb-4">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-600 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg> --}}
                        <i class="bi bi-bag-fill text-purple-600 dark:text-purple-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Supplier</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Setup and manage different suppliers.
                    </p>
                    <a href="{{ route('supplier.index') }}"
                        class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                        Open Supplier
                    </a>
                </div>
            </div>

            {{-- User --}}
            @if (Auth::user()->role == 1)
                <div
                    class="p-6 hover:bg-red-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-red-800 dark:border-red-700 transition">
                    <div class="flex flex-col items-center text-center">
                        <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full mb-4">
                            {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-600 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg> --}}
                            <i class="bi bi-person-fill-lock text-red-600 dark:text-red-300 text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">AIMS User</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            Setup and manage AIMS users.
                        </p>
                        <a href="{{ route('user.index') }}"
                            class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                            Open User Management
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
