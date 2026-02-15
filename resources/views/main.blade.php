@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="p-6 space-y-6">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Asset Management Dashboard</h1>
            <p class="text-sm text-gray-500">Real-time overview of agency assets and property</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white shadow rounded-lg p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Assets</p>
                    <p class="text-xl font-bold text-gray-900">{{ $totalAssets }}</p>
                </div>
                <div class="text-blue-500">
                    <i class="fas fa-box fa-2x"></i>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Active</p>
                    <p class="text-xl font-bold text-green-500">{{ $activeAssets }}</p>
                </div>
                <div class="text-green-500">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Assigned</p>
                    <p class="text-xl font-bold text-blue-500">{{ $assignedAssets }}</p>
                </div>
                <div class="text-blue-500">
                    <i class="fas fa-user-check fa-2x"></i>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Needs Attention</p>
                    <p class="text-xl font-bold text-red-500">{{ $maintenanceAssets }}</p>
                </div>
                <div class="text-red-500">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6">

            <!-- Asset Distribution -->
            <div class="lg:col-span-2 bg-white shadow rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Asset Distribution by Location</h2>
                <canvas id="assetChart" class="h-64"></canvas>
            </div>

            <!-- Asset Status -->
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Asset Status</h2>
                <canvas id="statusChart" class="h-64"></canvas>
            </div>
        </div>

        <!-- Categories and Alerts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6">

            <!-- Asset Categories -->
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Asset Categories</h2>
                <ul class="space-y-2">
                    @foreach ($categories as $category => $count)
                        <li class="flex justify-between items-center border-b py-2">
                            <span>{{ $category }}</span>
                            <span class="bg-gray-100 text-gray-800 text-sm rounded px-2 py-1">{{ $count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Recent Alerts -->
            <div class="bg-white shadow rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Alerts</h2>
                    <a href="#" class="text-sm text-blue-600 hover:underline">View All</a>
                </div>

                <ul class="space-y-2">

                    @forelse($alerts as $alert)
                        @php
                            $bgColor = match ($alert['severity']) {
                                4 => 'bg-red-50 border-red-200',
                                3 => 'bg-orange-50 border-orange-200',
                                2 => 'bg-yellow-50 border-yellow-200',
                                default => 'bg-blue-50 border-blue-200',
                            };

                            $badgeColor = match ($alert['severity']) {
                                4 => 'bg-red-200 text-red-800',
                                3 => 'bg-orange-200 text-orange-800',
                                2 => 'bg-yellow-200 text-yellow-800',
                                default => 'bg-blue-200 text-blue-800',
                            };

                            $label = match ($alert['severity']) {
                                4 => 'Critical',
                                3 => 'High',
                                2 => 'Medium',
                                default => 'Low',
                            };
                        @endphp

                        <li>
                            <a href="{{ $alert['url'] }}"
                                class="flex items-start gap-3 p-3 border rounded-lg {{ $bgColor }} hover:shadow transition">

                                <i class="bi {{ $alert['icon'] }} mt-1"></i>

                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">
                                        {{ $alert['type'] }}
                                        <span class="text-xs px-2 py-0.5 rounded {{ $badgeColor }}">
                                            {{ $label }}
                                        </span>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $alert['message'] }}
                                    </p>
                                </div>

                            </a>
                        </li>

                    @empty
                        <li class="text-sm text-gray-500">
                            No alerts at the moment.
                        </li>
                    @endforelse

                </ul>
            </div>


            {{-- <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Alerts</h2>
                <ul class="space-y-2">

                    <li class="flex items-start gap-2 p-2 border rounded bg-yellow-50">
                        <i class="fas fa-exclamation-circle text-yellow-500 mt-1"></i>
                        <div>
                            <p class="font-semibold">License Expiry <span
                                    class="text-xs bg-yellow-200 px-2 py-0.5 rounded">warning</span></p>
                            <p class="text-sm text-gray-600">Vehicle License VH-2024-001 expires in 15 days</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-2 p-2 border rounded bg-red-50">
                        <i class="fas fa-times-circle text-red-500 mt-1"></i>
                        <div>
                            <p class="font-semibold">Overdue Return <span
                                    class="text-xs bg-red-200 px-2 py-0.5 rounded">error</span></p>
                            <p class="text-sm text-gray-600">Radio RD-2024-145 overdue from Officer J. Smith</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-2 p-2 border rounded bg-blue-50">
                        <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                        <div>
                            <p class="font-semibold">Maintenance Due <span
                                    class="text-xs bg-blue-200 px-2 py-0.5 rounded">info</span></p>
                            <p class="text-sm text-gray-600">3 firearms due for quarterly maintenance</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-2 p-2 border rounded bg-red-50">
                        <i class="fas fa-times-circle text-red-500 mt-1"></i>
                        <div>
                            <p class="font-semibold">Missing Asset <span
                                    class="text-xs bg-red-200 px-2 py-0.5 rounded">error</span></p>
                            <p class="text-sm text-gray-600">Asset EQ-2024-089 not scanned in 48 hours</p>
                        </div>
                    </li>
                </ul>
            </div> --}}

        </div>

    </div>

    <!-- Charts Script -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    <script src="{{ asset('assets/js/chart.js') }}"></script>
    <script>
        const ctx = document.getElementById('assetChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($locationLabels) !!},
                datasets: [{
                    label: 'Assets',
                    data: {!! json_encode($assetData) !!},
                    backgroundColor: '#1F2937'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Assigned', 'Maintenance', 'Retired'],
                datasets: [{
                    data: [{{ $activeAssets }}, {{ $assignedAssets }}, {{ $maintenanceAssets }},
                        {{ $retiredAssets }}
                    ],
                    backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endsection
