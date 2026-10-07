@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;

    $mainModules = Auth::user()->accessibleSubModules(main: true)->get();

    // Link: route name looked up by sub-module code (config/submodule_routes.php), else a route named after the code.
    $routeMap = config('submodule_routes', []);
    $resolveLink = function ($sm) use ($routeMap) {
        $name = $routeMap[$sm->code] ?? $sm->code;
        return Route::has($name) ? route($name) : '#';
    };
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
                    <img width="48" height="48" src="https://img.icons8.com/color/48/dashboard-layout.png"
                        alt="dashboard" />
                    <span class="text-xs mt-1">Dashboard</span>
                </a>

                @foreach ($mainModules as $sm)
                    <a href="{{ $resolveLink($sm) }}" title="{{ $sm->description }}"
                        class="py-1.5 px-2.5 flex flex-col items-center gap-x-1.5 text-sm text-gray-800 bg-gray-100 hover:text-cyan-700 rounded-lg focus:outline-hidden focus:text-cyan-700 hover:bg-gray-200">
                        @php($imgSrc = $sm->src ?: $sm->img)
                        @if ($imgSrc)
                            <img width="48" height="48" src="{{ $imgSrc }}" alt="{{ $sm->name }}" />
                        @elseif ($sm->icon)
                            <i class="{{ $sm->icon }}" style="font-size: 48px; line-height: 48px;"></i>
                        @endif
                        <span class="text-xs mt-1">{{ $sm->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </x-slot>

    <main>
        @yield('content')
    </main>

</x-app-layout>
