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

            {{-- Cashier Dashboard --}}
            <div
                class="p-6 hover:bg-green-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-gray-800 dark:border-gray-700 transition">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full mb-4">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c.638 0 1.24.195 1.743.532A2.993 2.993 0 0118 12a3 3 0 01-6 0 3 3 0 010-6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12a10 10 0 1120 0 10 10 0 01-20 0z" />
                        </svg> --}}
                        <i class="bi bi-credit-card-2-front text-green-600 dark:text-green-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Cashier Dashboard</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Manage and process orders, handle payments, and monitor order status in real-time.
                    </p>
                    <a href="#" class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                        Open Cashier
                    </a>
                </div>
            </div>

            {{-- Kitchen Display --}}
            <div
                class="p-6 hover:bg-orange-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-gray-800 dark:border-gray-700 transition">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full mb-4">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-orange-600 dark:text-orange-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a9.956 9.956 0 00-6.364 2.364A9.956 9.956 0 002 12a9.956 9.956 0 002.364 6.364A9.956 9.956 0 0012 22a9.956 9.956 0 006.364-2.364A9.956 9.956 0 0022 12a9.956 9.956 0 00-2.364-6.364A9.956 9.956 0 0012 2z" />
                        </svg> --}}
                        <i class="bi bi-egg-fried text-orange-600 dark:text-orange-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Kitchen Display</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Real-time order tracking for kitchen staff with priority indicators and status updates.
                    </p>
                    <a href="#" class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                        Open Kitchen Display
                    </a>
                </div>
            </div>

            {{-- Customer Display --}}
            <div
                class="p-6 hover:bg-teal-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-gray-800 dark:border-gray-700 transition">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-teal-100 dark:bg-teal-900 rounded-full mb-4">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-teal-600 dark:text-teal-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg> --}}
                        <i class="bi bi-broadcast-pin text-teal-600 dark:text-teal-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Customer Display</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Public display showing ready orders for customer pickup with order numbers.
                    </p>
                    <a href="#" class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                        Open Customer Display
                    </a>
                </div>
            </div>

            {{-- Admin Panel --}}
            <div
                class="p-6 hover:bg-purple-100 focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-gray-800 dark:border-gray-700 transition">
                <div class="flex flex-col items-center text-center">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full mb-4">
                        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-600 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.5L12 2l2.25 1.5M4.5 8.25l.75 4.5M19.5 8.25l-.75 4.5M8.25 20.25h7.5" />
                        </svg> --}}
                        <i class="bi bi-gear text-purple-600 dark:text-purple-300 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Admin Panel</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Manage menu items, track inventory, and handle supply chain management.
                    </p>
                    <a href="" class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                        Open Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
