@php
    use Illuminate\Support\Facades\Auth;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 mt-0">
                <x-application-logo class="h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
            </div>

            <h2 class="ml-3 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mt-0">
                {{ env('APP_NAME', 'Asset and Workforce Management System') }}
            </h2>
        </div>

        <div class="py-0.5">
            <div class="flex flex-wrap justify-center gap-1.5 sm:gap-2">
                <a href="{{ route('main') }}" title="Real-time overview of agency assets and property"
                    class="py-1.5 px-2.5 flex flex-col items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700 hover:bg-gray-200">
                    {{-- <i class="bi bi-speedometer"></i> --}}
                    {{-- https://img.icons8.com/color/48/dashboard-layout.png --}}
                    <img width="48" height="48" src="{{ asset('icons/dashboard.png') }}"
                        alt="dashboard-layout" />
                    <span class="text-xs mt-1">Dashboard</span>
                </a>
                <a href="{{ route('asset.index') }}"
                    title ="Comprehensive inventory of all agency assets, including details and status"
                    class="py-1.5 px-2.5 flex flex-col items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700  hover:bg-gray-200">
                    <img width="48" height="48" src="{{ asset('icons/assets.png') }}" alt="box" />
                    {{-- https://img.icons8.com/color/48/box.png --}}
                    <span class="text-xs mt-1">Assets</span>
                </a>
                <a href="{{ route('supplies.index') }}" title="Management of agency supplies and inventory"
                    class="py-1.5 px-2.5 flex flex-col items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700  hover:bg-gray-200">
                    <img width="48" height="48" src="{{ asset('icons/supplies.png') }}"
                        alt="external-office-supplies-office-and-office-supplies-flaticons-lineal-color-flat-icons-11" />
                    <span class="text-xs mt-1">Supplies</span>
                    {{-- https://img.icons8.com/external-flaticons-lineal-color-flat-icons/64/external-office-supplies-office-and-office-supplies-flaticons-lineal-color-flat-icons-11.png --}}
                </a>
                <a href="{{ route('budget.index') }}" title="Request budget for office supplies and asset purchases"
                    class="py-1.5 px-2.5 flex flex-col items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700  hover:bg-gray-200">
                    <img width="48" height="48" src="{{ asset('icons/budget.png') }}"
                        alt="external-office-supplies-office-and-office-supplies-flaticons-lineal-color-flat-icons-11" />
                    <span class="text-xs mt-1">Budget</span>
                    {{-- https://img.icons8.com/?size=100&id=68348&format=png&color=000000 --}}
                </a>
                {{-- <a href=""
                    class="py-1.5 px-2.5 inline-flex items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700">
                    <i class="bi bi-upc-scan"></i>
                    Scanner
                </a> --}}

                {{-- https://img.icons8.com/external-good-lines-kalash/32/external-card-banking-and-money-good-lines-kalash.png --}}
                <a href="{{ route('asset-licenses.index') }}" title="Track and monitor asset licenses and permits"
                    class="py-1.5 px-2.5 flex flex-col items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700  hover:bg-gray-200">
                    <img width="48" height="48" src="{{ asset('icons/licenses.png') }}"
                        alt="external-card-banking-and-money-good-lines-kalash" />
                    Licenses
                </a>

                {{-- https://img.icons8.com/3d-sugary/100/document-14.png --}}
                <a href="{{ route('clearance.index') }}" title="Manage asset clearance and disposal processes"
                    class="py-1.5 px-2.5 flex flex-col items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700  hover:bg-gray-200">
                    <img width="48" height="48" src="{{ asset('icons/clearance.png') }}" alt="document-14" />
                    Clearance
                </a>

                {{-- https://img.icons8.com/external-wanicon-lineal-color-wanicon/64/external-wrench-construction-wanicon-lineal-color-wanicon.png --}}
                <a href="{{ route('maintenance.index') }}" title="Manage asset maintenance and repair activities"
                    class="py-1.5 px-2.5 flex flex-col items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700  hover:bg-gray-200">
                    <img width="48" height="48" src="{{ asset('icons/maintenance.png') }}"
                        alt="external-wrench-construction-wanicon-lineal-color-wanicon" />
                    Maintenance
                </a>

                {{-- https://img.icons8.com/external-filled-outline-wichaiwi/64/external-Employee-business-filled-outline-wichaiwi.png --}}
                <a href="{{ route('employee.index') }}" title="Manage employee information and records"
                    class="py-1.5 px-2.5 flex flex-col items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700  hover:bg-gray-200">
                    <img width="48" height="48" src="{{ asset('icons/employees.png') }}"
                        alt="external-Employee-business-filled-outline-wichaiwi" />
                    Employees
                </a>
                {{-- <a href="{{ route('location.index') }}"
                    class="py-1.5 px-2.5 inline-flex items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700">
                    <i class="bi bi-building"></i>
                    Location
                </a> --}}

                {{-- https://img.icons8.com/fluency/48/pie-chart-report-script.png --}}
                <a class="py-1.5 px-2.5 flex flex-col items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700  hover:bg-gray-200"
                    href="{{ route('reports.index') }}">
                    <img width="48" height="48" src="{{ asset('icons/reports.png') }}"
                        alt="pie-chart-report-script" />
                    Reports
                </a>
                @if (Auth::user()->role != 2)
                    {{-- https://img.icons8.com/bubbles/100/settings.png --}}
                    <a href="{{ route('setup.index') }}"
                        class="py-1.5 px-2.5 flex flex-col items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700  hover:bg-gray-200">
                        <img width="48" height="48" src="{{ asset('icons/settings.png') }}" alt="settings" />
                        Setup
                    </a>
                @endif
                {{-- <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="py-1.5 px-2.5 inline-flex items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700"
                        href="#">
                        <i class="bi bi-box-arrow-right"></i>
                        Signout
                    </button>
                </form> --}}
            </div>
        </div>
    </x-slot>

    <main>
        @yield('content')
    </main>

</x-app-layout>
