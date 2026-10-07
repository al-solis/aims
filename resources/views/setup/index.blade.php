@extends('dashboard')
@section('content')
    @php
        use Illuminate\Support\Facades\Auth;
        use Illuminate\Support\Facades\Route;

        $moduleCode = 'SET';

        // Sub-modules of Setup whose code does NOT end in "-01", limited to what the user's role can view.
$subModules = Auth::user()->accessibleSubModules($moduleCode, main: false)->get();

$routeMap = config('submodule_routes', []);
$resolveLink = function ($sm) use ($routeMap) {
    $name = $routeMap[$sm->code] ?? $sm->code;
    return Route::has($name) ? route($name) : '#';
};

$palette = [
    [
        'hover' => 'hover:bg-red-100',
        'bg' => 'bg-red-100 dark:bg-red-900',
        'text' => 'text-red-600 dark:text-red-300',
    ],
    [
        'hover' => 'hover:bg-blue-100',
        'bg' => 'bg-blue-100 dark:bg-blue-900',
        'text' => 'text-blue-600 dark:text-blue-300',
    ],
    [
        'hover' => 'hover:bg-yellow-100',
        'bg' => 'bg-yellow-100 dark:bg-yellow-900',
        'text' => 'text-yellow-600 dark:text-yellow-300',
    ],
    [
        'hover' => 'hover:bg-green-100',
        'bg' => 'bg-green-100 dark:bg-green-900',
        'text' => 'text-green-600 dark:text-green-300',
    ],
    [
        'hover' => 'hover:bg-orange-100',
        'bg' => 'bg-orange-100 dark:bg-orange-900',
        'text' => 'text-orange-600 dark:text-orange-300',
    ],
    [
        'hover' => 'hover:bg-indigo-100',
        'bg' => 'bg-indigo-100 dark:bg-indigo-900',
        'text' => 'text-indigo-600 dark:text-indigo-300',
    ],
    [
        'hover' => 'hover:bg-purple-100',
        'bg' => 'bg-purple-100 dark:bg-purple-900',
        'text' => 'text-purple-600 dark:text-purple-300',
    ],
    [
        'hover' => 'hover:bg-gray-100',
        'bg' => 'bg-gray-100 dark:bg-gray-900',
        'text' => 'text-gray-600 dark:text-gray-300',
            ],
        ];
    @endphp
    <div class="py-5">
        <div class="max-w-7xl mx-auto px-2 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">

            @forelse ($subModules as $sm)
                @php
                    $c = $palette[$loop->index % count($palette)];
                    $icon = $sm->icon ? (str_contains($sm->icon, 'bi ') ? $sm->icon : 'bi ' . $sm->icon) : null;
                    $imgSrc = $sm->src ?: $sm->img;
                @endphp
                <div
                    class="p-3 {{ $c['hover'] }} focus:outline-hidden bg-white border border-gray-200 rounded-2xl shadow hover:shadow-md dark:bg-gray-800 dark:border-gray-700 transition">
                    <div class="flex flex-col items-center text-center">
                        <div class="p-3 {{ $c['bg'] }} rounded-full mb-4">
                            @if ($icon)
                                <i class="{{ $icon }} {{ $c['text'] }} text-4xl"></i>
                            @elseif ($imgSrc)
                                <img width="40" height="40" src="{{ $imgSrc }}" alt="{{ $sm->name }}" />
                            @else
                                <i class="bi bi-gear {{ $c['text'] }} text-4xl"></i>
                            @endif
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{ $sm->name }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            {{ $sm->description }}
                        </p>
                        <a href="{{ $resolveLink($sm) }}"
                            class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-700 transition">
                            Open {{ $sm->name }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-gray-200 bg-white p-8 text-center text-gray-500">
                    You don't have access to any setup items.
                </div>
            @endforelse

        </div>
    </div>
@endsection
