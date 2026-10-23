@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;

        $money = fn($v) => '₱' . number_format((float) $v, 2); // change the currency symbol here if needed
        $moneyShort = function ($v) {
            $v = (float) $v;
            if ($v >= 1000000000) {
                return '₱' . number_format($v / 1000000000, 2) . 'B';
            }
            if ($v >= 1000000) {
                return '₱' . number_format($v / 1000000, 2) . 'M';
            }
            if ($v >= 1000) {
                return '₱' . number_format($v / 1000, 1) . 'K';
            }
            return '₱' . number_format($v, 2);
        };
    @endphp
    <div class="ft-page p-6 space-y-6 max-w-7xl mx-auto">

        <!-- Header & Filters -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
            <div>
                <h1 class="text-2xl font-semibold" style="color: var(--ft-ink);">Asset Management Dashboard</h1>
                <p class="text-sm mt-1" style="color: var(--ft-muted);">Real-time overview of agency assets and property</p>
            </div>

            <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
                <!-- Cluster Filter -->
                {{-- FIX: changing the cluster now clears the location, otherwise the old location was submitted
                     together with the new cluster and the two filters cancelled each other out (empty dashboard). --}}
                <select name="searchcluster" class="ft-field w-full sm:w-48"
                    onchange="this.form.searchloc.value = ''; this.form.submit()">
                    <option value="">All Clusters</option>
                    @foreach ($allClusters as $cluster)
                        <option value="{{ $cluster->id }}" {{ $filterClusterId == $cluster->id ? 'selected' : '' }}>
                            {{ $cluster->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Location Filter (already limited to the selected cluster by the controller) -->
                <select name="searchloc" class="ft-field w-full sm:w-48" onchange="this.form.submit()">
                    <option value="">All Locations</option>
                    @foreach ($allLocations as $location)
                        <option value="{{ $location->id }}" {{ $filterLocationId == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>

                @if ($filterClusterId || $filterLocationId)
                    <a href="{{ route('dashboard') }}" class="ft-btn ft-btn-ghost whitespace-nowrap">Clear Filters</a>
                @endif
            </form>
        </div>

        <!-- Asset Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Assets -->
            <a href="{{ route('asset.index') }}"
                class="ft-info flex items-center justify-between transition hover:border-[var(--ft-accent)] group"
                style="padding: 1rem;">
                <div>
                    <p class="ft-eyebrow">Total Assets</p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--ft-ink);">{{ $totalAssets }}</p>
                </div>
                <div class="p-3 rounded-full bg-[var(--ft-mist)] group-hover:bg-[var(--ft-accent-ring)] transition-colors">
                    <svg class="w-5 h-5" style="color: var(--ft-accent);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </a>

            <!-- Active Assets -->
            <a href="{{ route('asset.index', ['searchstat' => 2]) }}"
                class="ft-info flex items-center justify-between transition hover:border-[var(--ft-success-border)] group"
                style="padding: 1rem;">
                <div>
                    <p class="ft-eyebrow">Active</p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--ft-success-text);">{{ $activeAssets }}</p>
                </div>
                <div class="p-3 rounded-full bg-[var(--ft-mist)] group-hover:bg-[var(--ft-success-bg)] transition-colors">
                    <svg class="w-5 h-5" style="color: var(--ft-success-text);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </a>

            <!-- Assigned Assets -->
            <a href="{{ route('asset.index', ['searchstat' => 3]) }}"
                class="ft-info flex items-center justify-between transition hover:border-[var(--ft-accent)] group"
                style="padding: 1rem;">
                <div>
                    <p class="ft-eyebrow">Assigned</p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--ft-ink);">{{ $assignedAssets }}</p>
                </div>
                <div class="p-3 rounded-full bg-[var(--ft-mist)] group-hover:bg-[var(--ft-accent-ring)] transition-colors">
                    <svg class="w-5 h-5" style="color: var(--ft-accent);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </a>

            <!-- Needs Attention -->
            <a href="{{ route('asset.index', ['searchstat' => 4]) }}"
                class="ft-info flex items-center justify-between transition hover:border-[var(--ft-danger-border)] group"
                style="padding: 1rem;">
                <div>
                    <p class="ft-eyebrow">Needs Attention</p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--ft-danger-text);">{{ $maintenanceAssets }}</p>
                </div>
                <div class="p-3 rounded-full bg-[var(--ft-mist)] group-hover:bg-[var(--ft-danger-bg)] transition-colors">
                    <svg class="w-5 h-5" style="color: var(--ft-danger-text);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </a>
        </div>

        <!-- Employee & Location Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <!-- Total Employees -->
            <a href="{{ route('employee.index') }}"
                class="ft-info flex items-center justify-between transition hover:border-[var(--ft-accent)] group"
                style="padding: 1rem;">
                <div>
                    <p class="ft-eyebrow">Total Employees</p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--ft-ink);">{{ $totalEmployees }}</p>
                </div>
                <div class="p-3 rounded-full bg-[var(--ft-mist)] group-hover:bg-[var(--ft-accent-ring)] transition-colors">
                    <svg class="w-5 h-5" style="color: var(--ft-accent);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </a>

            <!-- Active Employees -->
            <a href="{{ route('employee.index', ['status' => 1]) }}"
                class="ft-info flex items-center justify-between transition hover:border-[var(--ft-success-border)] group"
                style="padding: 1rem;">
                <div>
                    <p class="ft-eyebrow">Active Employees</p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--ft-success-text);">{{ $activeEmployees }}</p>
                </div>
                <div class="p-3 rounded-full bg-[var(--ft-mist)] group-hover:bg-[var(--ft-success-bg)] transition-colors">
                    <svg class="w-5 h-5" style="color: var(--ft-success-text);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </a>

            <!-- Unassigned Employees -->
            <a href="{{ route('employee.index', ['searchloc' => 'null']) }}"
                class="ft-info flex items-center justify-between transition hover:border-[var(--ft-warning-border)] group"
                style="padding: 1rem;">
                <div>
                    <p class="ft-eyebrow">Unassigned Employees</p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--ft-warning-text);">{{ $unassignedEmployees }}
                    </p>
                </div>
                <div class="p-3 rounded-full bg-[var(--ft-mist)] group-hover:bg-[var(--ft-warning-bg)] transition-colors">
                    <svg class="w-5 h-5" style="color: var(--ft-warning-text);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </a>

            <!-- Total Clusters -->
            <a href="{{ route('cluster.index') }}"
                class="ft-info flex items-center justify-between transition hover:border-[var(--ft-accent)] group"
                style="padding: 1rem;">
                <div>
                    <p class="ft-eyebrow">Active Clusters</p>
                    <p class="text-xl font-bold mt-0.5" style="color: var(--ft-ink);">{{ $totalClusters }}</p>
                </div>
                <div class="p-3 rounded-full bg-[var(--ft-mist)] group-hover:bg-[var(--ft-accent-ring)] transition-colors">
                    <svg class="w-5 h-5" style="color: var(--ft-accent);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </a>
        </div>

        <!-- NEW: Management KPIs -->
        @php
            $kpis = [
                [
                    'label' => 'Total Asset Value',
                    'value' => $moneyShort($totalAssetValue),
                    'title' => $money($totalAssetValue),
                    'hint' => 'Acquisition cost, excl. retired',
                    'color' => 'var(--ft-ink)',
                ],
                [
                    'label' => 'Utilization Rate',
                    'value' => $utilizationRate . '%',
                    'title' => 'Assigned ÷ (Active + Assigned)',
                    'hint' => $assignedAssets . ' of ' . ($activeAssets + $assignedAssets) . ' usable assets in use',
                    'color' => $utilizationRate >= 60 ? 'var(--ft-success-text)' : 'var(--ft-warning-text)',
                ],
                [
                    'label' => 'Assets per Employee',
                    'value' => $assetsPerEmployee,
                    'title' => 'Assigned assets ÷ active employees',
                    'hint' => 'Assigned assets per active employee',
                    'color' => 'var(--ft-ink)',
                ],
                [
                    'label' => 'Workforce Movement (30d)',
                    'value' => '+' . $newHires30 . ' / -' . $separations30,
                    'title' => 'New hires / separations in the last 30 days',
                    'hint' => 'New hires / separations',
                    'color' => 'var(--ft-ink)',
                ],
            ];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            @foreach ($kpis as $kpi)
                <div class="ft-info" style="padding: 1rem;" title="{{ $kpi['title'] }}">
                    <p class="ft-eyebrow">{{ $kpi['label'] }}</p>
                    <p class="text-xl font-bold mt-0.5" style="color: {{ $kpi['color'] }};">{{ $kpi['value'] }}</p>
                    <p class="text-xs mt-1" style="color: var(--ft-muted);">{{ $kpi['hint'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- NEW: Operational Health -->
        @php
            $opsTiles = [
                ['Expired Licenses', $ops['license_expired'], 'danger', route('asset-licenses.index', 0)],
                ['Licenses Expiring Soon', $ops['license_expiring'], 'warning', route('asset-licenses.index', 0)],
                ['Overdue Maintenance', $ops['maint_overdue'], 'danger', route('maintenance.index')],
                ['Maintenance Due Soon', $ops['maint_due'], 'warning', route('maintenance.index')],
                ['Overdue Clearances', $ops['clearance_overdue'], 'danger', route('clearance.index')],
                ['Clearances Due Soon', $ops['clearance_due'], 'warning', route('clearance.index')],
                ['Out-of-Stock Supplies', $ops['supplies_out'], 'danger', route('supplies.index')],
                ['Low-Stock Supplies', $ops['supplies_low'], 'warning', route('supplies.index')],
            ];
        @endphp
        <div class="ft-card">
            <div class="p-4 flex justify-between items-center border-b" style="border-color: var(--ft-line);">
                <h2 class="text-base font-bold" style="color: var(--ft-ink);">Operational Health</h2>
                @if ($assignedNoOwner > 0)
                    <span class="text-xs" style="color: var(--ft-warning-text);">
                        {{ $assignedNoOwner }} asset(s) marked "Assigned" have no assignee
                    </span>
                @endif
            </div>
            <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach ($opsTiles as [$label, $count, $tone, $url])
                    <a href="{{ $url }}" class="border rounded-lg p-3 hover:shadow transition"
                        style="border-color: var(--ft-line);">
                        <p class="text-xs" style="color: var(--ft-muted);">{{ $label }}</p>
                        <p class="text-lg font-bold mt-0.5"
                            style="color: {{ $count > 0 ? 'var(--ft-' . $tone . '-text)' : 'var(--ft-success-text)' }};">
                            {{ $count }}</p>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Asset Distribution -->
            <div class="lg:col-span-2 ft-card">
                <div class="p-4 border-b" style="border-color: var(--ft-line);">
                    <h2 class="text-base font-bold" style="color: var(--ft-ink);">Asset Distribution by Location</h2>
                </div>
                <div class="p-4">
                    <div id="assetChart" class="h-64"></div>
                </div>
            </div>

            <!-- Asset Status -->
            <div class="ft-card">
                <div class="p-4 border-b" style="border-color: var(--ft-line);">
                    <h2 class="text-base font-bold" style="color: var(--ft-ink);">Asset Status</h2>
                </div>
                <div class="p-4">
                    <div id="statusChart" class="h-64 flex justify-center"></div>
                </div>
            </div>
        </div>

        <!-- Secondary Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Employees by Location -->
            <div class="ft-card">
                <div class="p-4 border-b" style="border-color: var(--ft-line);">
                    <h2 class="text-base font-bold" style="color: var(--ft-ink);">Employees by Location</h2>
                </div>
                <div class="p-4">
                    <div id="employeeChart" class="h-64"></div>
                </div>
            </div>

            <!-- Assets by Cluster -->
            <div class="ft-card">
                <div class="p-4 border-b" style="border-color: var(--ft-line);">
                    <h2 class="text-base font-bold" style="color: var(--ft-ink);">Assets by Cluster</h2>
                </div>
                <div class="p-4">
                    <div id="clusterChart" class="h-64 flex justify-center"></div>
                </div>
            </div>
        </div>

        <!-- NEW: Acquisition trend + Condition -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div class="lg:col-span-2 ft-card">
                <div class="p-4 border-b" style="border-color: var(--ft-line);">
                    <h2 class="text-base font-bold" style="color: var(--ft-ink);">Asset Acquisitions (Last 12 Months)</h2>
                </div>
                <div class="p-4">
                    <div id="acquisitionChart" class="h-64"></div>
                </div>
            </div>

            <div class="ft-card">
                <div class="p-4 border-b" style="border-color: var(--ft-line);">
                    <h2 class="text-base font-bold" style="color: var(--ft-ink);">Asset Condition</h2>
                </div>
                <div class="p-4">
                    <div id="conditionChart" class="h-64 flex justify-center"></div>
                </div>
            </div>
        </div>

        <!-- NEW: Value by category / Top locations / Employment type -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Asset value by category -->
            <div class="ft-card">
                <div class="p-4 border-b" style="border-color: var(--ft-line);">
                    <h2 class="text-base font-bold" style="color: var(--ft-ink);">Asset Value by Category</h2>
                </div>
                <div class="p-4">
                    @php $maxCatValue = max(1, (float) $categoryValue->max()); @endphp
                    <ul class="space-y-3">
                        @forelse ($categoryValue as $name => $value)
                            <li>
                                <div class="flex justify-between text-sm">
                                    <span style="color: var(--ft-text);">{{ $name }}</span>
                                    <span class="font-medium"
                                        style="color: var(--ft-ink);">{{ $moneyShort($value) }}</span>
                                </div>
                                <div class="h-1.5 rounded-full mt-1" style="background: var(--ft-mist);">
                                    <div class="h-1.5 rounded-full"
                                        style="width: {{ round(($value / $maxCatValue) * 100) }}%; background: var(--ft-accent);">
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm py-4 text-center" style="color: var(--ft-muted);">No data.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Top locations by value -->
            <div class="ft-card">
                <div class="p-4 border-b" style="border-color: var(--ft-line);">
                    <h2 class="text-base font-bold" style="color: var(--ft-ink);">Top Locations by Asset Value</h2>
                </div>
                <div class="p-4">
                    <ul class="space-y-2">
                        @forelse ($topLocations as $i => $loc)
                            <li class="flex justify-between items-center border-b py-2"
                                style="border-color: var(--ft-line);">
                                <div>
                                    <p class="text-sm font-medium" style="color: var(--ft-text);">{{ $i + 1 }}.
                                        {{ $loc['name'] }}</p>
                                    <p class="text-xs" style="color: var(--ft-muted);">{{ $loc['count'] }} asset(s)</p>
                                </div>
                                <span class="text-sm font-semibold"
                                    style="color: var(--ft-ink);">{{ $moneyShort($loc['value']) }}</span>
                            </li>
                        @empty
                            <li class="text-sm py-4 text-center" style="color: var(--ft-muted);">No data.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Employment type -->
            <div class="ft-card">
                <div class="p-4 border-b" style="border-color: var(--ft-line);">
                    <h2 class="text-base font-bold" style="color: var(--ft-ink);">Active Employees by Type</h2>
                </div>
                <div class="p-4">
                    <div id="employmentChart" class="h-64 flex justify-center"></div>
                </div>
            </div>
        </div>

        <!-- Categories and Alerts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Asset Categories -->
            <div class="ft-card">
                <div class="p-4 border-b" style="border-color: var(--ft-line);">
                    <h2 class="text-base font-bold" style="color: var(--ft-ink);">Asset Categories</h2>
                </div>
                <div class="p-4">
                    <ul class="space-y-2">
                        @forelse ($categories as $category => $count)
                            <li class="flex justify-between items-center border-b py-2"
                                style="border-color: var(--ft-line);">
                                <span class="text-sm font-medium"
                                    style="color: var(--ft-text);">{{ $category }}</span>
                                <span class="ft-badge ft-badge-na">{{ $count }}</span>
                            </li>
                        @empty
                            <li class="text-sm py-4 text-center" style="color: var(--ft-muted);">No categories found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Recent Alerts -->
            <div class="ft-card">
                <div class="p-4 flex justify-between items-center border-b" style="border-color: var(--ft-line);">
                    <h2 class="text-base font-bold" style="color: var(--ft-ink);">
                        Recent Alerts
                        @if ($totalAlerts > $alerts->count())
                            <span class="text-xs font-normal" style="color: var(--ft-muted);">(top {{ $alerts->count() }}
                                of {{ $totalAlerts }})</span>
                        @endif
                    </h2>
                    <a href="#" class="text-sm hover:underline" style="color: var(--ft-accent);">View All</a>
                </div>

                <ul class="p-4 space-y-3">
                    @forelse($alerts as $alert)
                        @php
                            $severityMap = [
                                4 => [
                                    'bg' => 'bg-[var(--ft-danger-bg)]',
                                    'border' => 'border-[var(--ft-danger-border)]',
                                    'text' => 'text-[var(--ft-danger-text)]',
                                    'label' => 'Critical',
                                ],
                                3 => [
                                    'bg' => 'bg-[var(--ft-orange-bg)]',
                                    'border' => 'border-[var(--ft-orange-border)]',
                                    'text' => 'text-[var(--ft-orange-text)]',
                                    'label' => 'High',
                                ],
                                2 => [
                                    'bg' => 'bg-[var(--ft-warning-bg)]',
                                    'border' => 'border-[var(--ft-warning-border)]',
                                    'text' => 'text-[var(--ft-warning-text)]',
                                    'label' => 'Medium',
                                ],
                                1 => [
                                    'bg' => 'bg-[var(--ft-mist)]',
                                    'border' => 'border-[var(--ft-line)]',
                                    'text' => 'text-[var(--ft-muted)]',
                                    'label' => 'Low',
                                ],
                            ];
                            $style = $severityMap[$alert['severity']] ?? $severityMap[1];
                        @endphp

                        <li>
                            <a href="{{ $alert['url'] }}"
                                class="flex items-start gap-3 p-3 border rounded-lg {{ $style['bg'] }} {{ $style['border'] }} hover:shadow transition">
                                <div class="mt-0.5 {{ $style['text'] }}">
                                    <i class="bi {{ $alert['icon'] }} text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <p class="font-semibold text-sm" style="color: var(--ft-ink);">
                                            {{ $alert['type'] }}</p>
                                        <span
                                            class="text-xs px-2 py-0.5 rounded {{ $style['bg'] }} {{ $style['text'] }} border {{ $style['border'] }}">
                                            {{ $style['label'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs mt-1" style="color: var(--ft-muted);">{{ $alert['message'] }}</p>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="text-center py-6 text-sm" style="color: var(--ft-muted);">
                            <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            No active alerts at the moment.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Charts Script -->
    <script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const style = getComputedStyle(document.documentElement);
            const inkColor = style.getPropertyValue('--ft-ink').trim() || '#1F2937';
            const textColor = style.getPropertyValue('--ft-text').trim() || '#374151';
            const mutedColor = style.getPropertyValue('--ft-muted').trim() || '#6B7280';
            const successColor = style.getPropertyValue('--ft-success-solid').trim() || '#10B981';
            const warningColor = style.getPropertyValue('--ft-warning-text').trim() || '#F59E0B';
            const dangerColor = style.getPropertyValue('--ft-danger-solid').trim() || '#EF4444';
            const accentColor = style.getPropertyValue('--ft-accent').trim() || '#3B82F6';
            const lineColor = style.getPropertyValue('--ft-line').trim() || '#e5e7eb';
            const surfaceColor = style.getPropertyValue('--ft-surface').trim() || '#ffffff';
            const onInkColor = style.getPropertyValue('--ft-on-ink').trim() || '#ffffff';

            // Render a chart, or a friendly message when there is nothing to plot
            function renderChart(selector, options, hasData) {
                const el = document.querySelector(selector);
                if (!el) return;
                if (!hasData) {
                    el.innerHTML = '<p class="text-sm text-center w-full py-10" style="color:' + mutedColor +
                        ';">No data to display.</p>';
                    return;
                }
                new ApexCharts(el, options).render();
            }
            const sum = (arr) => arr.reduce((a, b) => a + Number(b || 0), 0);
            const peso = (v) => '₱' + Number(v || 0).toLocaleString(undefined, {
                maximumFractionDigits: 0
            });

            // 1. Asset Distribution Chart
            var barColors = [inkColor, accentColor, successColor, warningColor, dangerColor, '#8B5CF6', '#EC4899',
                '#14B8A6'
            ];
            const assetData = {!! json_encode($assetData) !!};
            const locationLabels = {!! json_encode($locationLabels) !!};

            renderChart("#assetChart", {
                series: [{
                    name: 'Assets',
                    data: assetData
                }],
                chart: {
                    height: 300,
                    type: 'bar',
                    toolbar: {
                        show: false
                    }
                },
                colors: barColors,
                plotOptions: {
                    bar: {
                        columnWidth: '45%',
                        distributed: true,
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: false
                },
                xaxis: {
                    categories: locationLabels,
                    labels: {
                        style: {
                            colors: mutedColor,
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: mutedColor
                        }
                    }
                },
                grid: {
                    borderColor: lineColor
                }
            }, assetData.length > 0);

            // 2. Asset Status Chart
            const statusSeries = [{{ $activeAssets }}, {{ $assignedAssets }}, {{ $maintenanceAssets }},
                {{ $retiredAssets }}
            ];
            renderChart("#statusChart", {
                labels: ['Active', 'Assigned', 'Maintenance', 'Retired'],
                series: statusSeries,
                chart: {
                    type: 'donut',
                    height: 300
                },
                colors: [successColor, accentColor, warningColor, mutedColor],
                stroke: {
                    colors: [surfaceColor]
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: [onInkColor]
                    }
                }
            }, sum(statusSeries) > 0);

            // 3. Employees by Location Chart
            const employeeData = {!! json_encode($employeeData) !!};
            renderChart("#employeeChart", {
                series: [{
                    name: 'Employees',
                    data: employeeData
                }],
                chart: {
                    height: 300,
                    type: 'bar',
                    toolbar: {
                        show: false
                    }
                },
                colors: [accentColor],
                plotOptions: {
                    bar: {
                        columnWidth: '45%',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: locationLabels,
                    labels: {
                        style: {
                            colors: mutedColor,
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: mutedColor
                        }
                    }
                },
                grid: {
                    borderColor: lineColor
                }
            }, employeeData.length > 0);

            // 4. Assets by Cluster Chart
            const clusterSeries = {!! json_encode($assetClusterData) !!};
            renderChart("#clusterChart", {
                labels: {!! json_encode($clusterLabels) !!},
                series: clusterSeries,
                chart: {
                    type: 'pie',
                    height: 300
                },
                colors: [inkColor, accentColor, successColor, warningColor, dangerColor, '#8B5CF6'],
                stroke: {
                    colors: [surfaceColor]
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: [onInkColor]
                    }
                }
            }, sum(clusterSeries) > 0);

            // 5. NEW: Acquisitions (count + spend) over the last 12 months
            const acqCount = {!! json_encode($acquisitionCount) !!};
            const acqSpend = {!! json_encode($acquisitionSpend) !!};
            renderChart("#acquisitionChart", {
                series: [{
                        name: 'Assets acquired',
                        type: 'column',
                        data: acqCount
                    },
                    {
                        name: 'Spend',
                        type: 'line',
                        data: acqSpend
                    }
                ],
                chart: {
                    height: 300,
                    type: 'line',
                    toolbar: {
                        show: false
                    }
                },
                colors: [accentColor, warningColor],
                stroke: {
                    width: [0, 3],
                    curve: 'smooth'
                },
                plotOptions: {
                    bar: {
                        columnWidth: '45%',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: {!! json_encode($acquisitionLabels) !!},
                    labels: {
                        style: {
                            colors: mutedColor,
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: [{
                        title: {
                            text: 'Assets',
                            style: {
                                color: mutedColor
                            }
                        },
                        labels: {
                            style: {
                                colors: mutedColor
                            },
                            formatter: (v) => Math.round(v)
                        }
                    },
                    {
                        opposite: true,
                        title: {
                            text: 'Spend',
                            style: {
                                color: mutedColor
                            }
                        },
                        labels: {
                            style: {
                                colors: mutedColor
                            },
                            formatter: peso
                        }
                    }
                ],
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    }
                },
                grid: {
                    borderColor: lineColor
                }
            }, sum(acqCount) > 0);

            // 6. NEW: Asset condition
            const conditionSeries = {!! json_encode($conditionData) !!};
            renderChart("#conditionChart", {
                labels: {!! json_encode($conditionLabels) !!},
                series: conditionSeries,
                chart: {
                    type: 'donut',
                    height: 300
                },
                colors: [successColor, accentColor, warningColor, dangerColor, mutedColor, '#8B5CF6'],
                stroke: {
                    colors: [surfaceColor]
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: [onInkColor]
                    }
                }
            }, sum(conditionSeries) > 0);

            // 7. NEW: Active employees by employment type
            const employmentSeries = {!! json_encode($employmentTypeData) !!};
            renderChart("#employmentChart", {
                labels: {!! json_encode($employmentTypeLabels) !!},
                series: employmentSeries,
                chart: {
                    type: 'donut',
                    height: 300
                },
                colors: [accentColor, successColor, warningColor, inkColor, '#8B5CF6', '#14B8A6'],
                stroke: {
                    colors: [surfaceColor]
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: textColor
                    }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: [onInkColor]
                    }
                }
            }, sum(employmentSeries) > 0);
        });
    </script>
@endsection
