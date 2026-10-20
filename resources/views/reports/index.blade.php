@extends('dashboard')
@section('content')
    @php
        use Illuminate\Support\Facades\Auth;

        // modules.code of the Reports module — change to match your modules table.
        $moduleCode = 'REP';

        // sub-module code => ['key' => modal/report key, 'badge' => label]  (config/report_keys.php)
        $reportMap = config('report_keys', []);

        // Reports sub-modules (code NOT ending in "-01") the user's role can view, and that have a report mapped.
$reportCards = Auth::user()
    ->accessibleSubModules($moduleCode, main: false)
    ->get()
    ->filter(fn($sm) => isset($reportMap[$sm->code]))
    ->values();

$allowedReports = $reportCards->map(fn($sm) => $reportMap[$sm->code]['key'])->all();

$palette = [
    ['bg' => 'bg-blue-100 dark:bg-blue-900', 'text' => 'text-blue-600 dark:text-blue-300'],
    ['bg' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-600 dark:text-green-300'],
    ['bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-600 dark:text-yellow-300'],
    ['bg' => 'bg-orange-100 dark:bg-orange-900', 'text' => 'text-orange-600 dark:text-orange-300'],
    ['bg' => 'bg-purple-100 dark:bg-purple-900', 'text' => 'text-purple-600 dark:text-purple-300'],
    ['bg' => 'bg-indigo-100 dark:bg-indigo-900', 'text' => 'text-indigo-600 dark:text-indigo-300'],
    ['bg' => 'bg-red-100 dark:bg-red-900', 'text' => 'text-red-600 dark:text-red-300'],
        ];
    @endphp

    {{-- @php
        use Illuminate\Support\Facades\DB;
        $u = Auth::user();
        dd([
            'role_id' => $u->role_id,
            'config_keys' => array_keys(config('report_keys', [])),
            'rights_rows' => DB::table('access_rights')
                ->where('role_id', $u->role_id)
                ->where('module_id', 8)
                ->get(['sub_module_id', 'can_read']),
            'query_rows' => $u->accessibleSubModules('REP', main: false)->pluck('sub_modules.code'),
        ]);
    @endphp --}}
    <div class="container mx-auto px-2 sm:px-4 lg:px-4 py-2">
        <!-- Header -->
        {{-- <div class="mb-2">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Reports</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Select a report to generate</p>
        </div> --}}

        <!-- Reports Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-4">
            @forelse ($reportCards as $sm)
                @php
                    $c = $palette[$loop->index % count($palette)];
                    $cfg = $reportMap[$sm->code];
                    $icon = $sm->icon
                        ? (str_contains($sm->icon, 'bi ')
                            ? $sm->icon
                            : 'bi ' . $sm->icon)
                        : 'bi bi-file-earmark-text';
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 {{ $c['bg'] }} rounded-lg">
                                <i class="{{ $icon }} {{ $c['text'] }} text-2xl leading-none"></i>
                            </div>
                            @if (!empty($cfg['badge']))
                                <span
                                    class="text-xs font-medium {{ $c['text'] }} {{ $c['bg'] }} px-2 py-1 rounded">{{ $cfg['badge'] }}</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $sm->name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $sm->description }}</p>
                        <button onclick="openReportModal('{{ $cfg['key'] }}')"
                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Generate Report
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-lg border border-gray-200 bg-white p-8 text-center text-sm text-gray-500">
                    You don't have access to any reports.
                </div>
            @endforelse
        </div>
    </div>


    <!-- Parameter Modal -->
    <div id="reportModal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-4 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <div>
                        <h3 id="modalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">
                            Report Parameters
                        </h3>
                        <p id="modalDescription" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Set parameters to generate your report
                        </p>
                    </div>
                    <button type="button" onclick="closeReportModal()"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <!-- Dynamic Form Container -->
                <div id="formContainer" class="overflow-y-auto max-h-[70vh]">
                    <!-- Forms will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes rl-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes rl-slide {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(300%);
            }
        }

        @keyframes rl-pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        @keyframes rl-pop {
            from {
                opacity: 0;
                transform: translateY(10px) scale(.96);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes rl-blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .3;
            }
        }

        .rl-card {
            animation: rl-pop .25s ease-out;
        }

        .rl-ring {
            background: conic-gradient(from 0deg, rgba(37, 99, 235, 0) 0%, #2563eb 60%, #7c3aed 100%);
            -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 6px), #000 calc(100% - 5px));
            mask: radial-gradient(farthest-side, transparent calc(100% - 6px), #000 calc(100% - 5px));
            animation: rl-spin 1.2s linear infinite;
        }

        .rl-icon {
            animation: rl-pulse 1.8s ease-in-out infinite;
        }

        .rl-bar {
            animation: rl-slide 1.4s ease-in-out infinite;
        }

        .rl-dot-active {
            animation: rl-blink 1s ease-in-out infinite;
        }
    </style>

    <!-- Report Loading Overlay -->
    <div id="reportLoading" class="hidden fixed inset-0 z-[60] items-center justify-center bg-gray-900/60 backdrop-blur-sm"
        role="alertdialog" aria-live="polite" aria-labelledby="reportLoadingTitle">
        <div class="rl-card w-full max-w-md mx-4 rounded-2xl bg-white dark:bg-gray-800 shadow-2xl p-8 text-center">

            <!-- Animated icon -->
            <div class="relative mx-auto h-24 w-24">
                <div class="rl-ring absolute inset-0 rounded-full"></div>
                <div
                    class="rl-icon absolute inset-3 flex items-center justify-center rounded-full bg-blue-50 dark:bg-gray-700">
                    <svg class="h-9 w-9 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h3 id="reportLoadingTitle" class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">
                Generating report
            </h3>
            <p id="reportLoadingName" class="mt-1 text-sm font-medium text-blue-600 dark:text-blue-300"></p>

            <!-- Indeterminate progress bar -->
            <div class="mt-6 h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                <div class="rl-bar h-full w-1/3 rounded-full bg-gradient-to-r from-blue-500 to-purple-500"></div>
            </div>

            <!-- Status message -->
            <p id="reportLoadingMsg" class="mt-4 min-h-[2.5rem] text-sm text-gray-600 dark:text-gray-400">
                Collecting data...
            </p>

            <!-- Steps -->
            <ol class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <li data-step="0" class="rl-step flex items-center gap-1.5">
                    <span class="rl-dot h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-600"></span>Collect
                </li>
                <li class="h-px w-6 bg-gray-300 dark:bg-gray-600"></li>
                <li data-step="1" class="rl-step flex items-center gap-1.5">
                    <span class="rl-dot h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-600"></span>Build
                </li>
                <li class="h-px w-6 bg-gray-300 dark:bg-gray-600"></li>
                <li data-step="2" class="rl-step flex items-center gap-1.5">
                    <span class="rl-dot h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-600"></span>Finalize
                </li>
            </ol>

            <!-- Footer -->
            <div
                class="mt-6 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4 text-xs text-gray-500 dark:text-gray-400">
                <span>Please don't close this page</span>
                <span>Elapsed: <span id="reportLoadingTime"
                        class="font-mono font-medium text-gray-700 dark:text-gray-200">00:00</span></span>
            </div>
        </div>
    </div>

    <script>
        // Report configurations
        const reportConfigs = {
            'asset-summary': {
                title: 'Asset Listing Report',
                description: 'Select parameters for asset listing report. Filter by date range, category, status and location.',
                form: `
            <form id="reportForm" class="space-y-4 ml-1 mr-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Purchase Date/ Inventory Date</label>
                        <select name="date_range" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="this_quarter">This Quarter</option>
                            <option value="this_year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Report Type</label>
                        <select name="reptype" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="summary">Summary</option>
                            <option value="monthly-inventory">Monthly Inventory</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Asset Category</label>
                        <select name="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="">All Categories</option>
                            @foreach ($categories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Status</label>
                        <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="">All Status</option>
                            <option value="1">Available</option>
                            <option value="2">Active</option>
                            <option value="3">Assigned</option>
                            <option value="4">Under Maintenance</option>
                            <option value="5">Retired</option>
                            <option value="6">Lost</option>
                            <option value="7">Damaged</option>
                            <option value="8">Vault</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Location</label>
                        <select name="location" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="">All Locations</option>
                            @foreach ($locations ?? [] as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Sort By</label>
                        <select name="sort" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="name">Asset Name</option>
                            <option value="purchase_date">Purchase Date</option>
                            <option value="cost">Cost</option>
                        </select>
                    </div>
                </div>

                <div id="customDateRange" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">From Date</label>
                        <input type="date" name="from_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">To Date</label>
                        <input type="date" name="to_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    </div>
                </div>
                
                <div class="flex items-center space-x-2 mt-4">
                    <input type="checkbox" id="include_subcategories" name="include_subcategories" 
                        class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500">
                    <label for="include_subcategories" class="text-sm font-medium text-gray-900 dark:text-white">
                        Include subcategories
                    </label>
                </div>

                <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t dark:border-gray-600">
                    <button type="button" onclick="closeReportModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                        Generate Report
                    </button>
                </div>
            </form>
        `
            },
            'odometer': {
                title: 'Odometer Readings Report',
                description: 'Select date range for odometer readings',
                form: `
            <form id="reportForm" class="space-y-4 ml-1 mr-1">
                <div class="space-y-4">
                    <div>
                        <label for="asset_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Select Vehicle</label>
                        <select name="asset_id" id="asset_id" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" required>
                            <option value="">Choose a vehicle...</option>
                            @foreach ($vehicles ?? [] as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->name }} - {{ $vehicle->asset_code }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">From Date</label>
                            <input type="date" name="from_date" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" 
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">To Date</label>
                            <input type="date" name="to_date" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" 
                                required>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="radio" id="pdf" name="format" value="pdf" checked
                                class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 focus:ring-gray-500">
                            <label for="pdf" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">PDF</label>
                        </div>                        
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t dark:border-gray-600">
                    <button type="button" onclick="closeReportModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                        Generate Report
                    </button>
                </div>
            </form>
        `
            },
            'maintenance': {
                title: 'Maintenance History Report',
                description: 'Select parameters for maintenance report',
                form: `
        <form id="reportForm" class="space-y-4 ml-1 mr-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Date Range</label>
                    <select name="date_range" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Maintenance Type</label>
                    <select name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="0">All Types</option>
                        <option value="1">Preventive</option>
                        <option value="2">Corrective</option>
                        <option value="3">Emergency</option>
                        <option value="4">Inspection</option>
                    </select>
                </div>
            </div>

            <div id="customDateRange" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">From Date</label>
                    <input type="date" name="from_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">To Date</label>
                    <input type="date" name="to_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Include in Report</label>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="include_costs" name="include_costs" checked
                            class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500">
                        <label for="include_costs" class="ml-2 text-sm text-gray-900 dark:text-white">Cost breakdown</label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="include_technicians" name="include_technicians"
                            class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500">
                        <label for="include_technicians" class="ml-2 text-sm text-gray-900 dark:text-white">Technician details</label>
                    </div>
                </div>
            </div>

            <!-- Add format selection like odometer report -->
            <div class="flex items-center space-x-4 mt-4">
                <div class="flex items-center">
                    <input type="radio" id="maintenance_pdf" name="format" value="pdf" checked
                        class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 focus:ring-gray-500">
                    <label for="maintenance_pdf" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">PDF</label>
                </div>                
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t dark:border-gray-600">
                <button type="button" onclick="closeReportModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                    Generate Report
                </button>
            </div>
        </form>
    `
            },
            'employee': {
                title: 'Employee Listing',
                description: 'Select parameters for employee listing report',
                form: `
        <form id="reportForm" class="space-y-4 ml-1 mr-1">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Report Type</label>
                    <select name="report_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="1">Default</option>
                        <option value="2">Monthly Disposition Report</option>
                        <option value="3">Expiring LESP</option>
                        <option value="4">Summary Report</option>
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Date Range</label>
                    <select name="date_range" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                
                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Status</label>
                    <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="0">Inactive</option>
                        <option value="1">Active</option>
                        <option value="2">On Leave</option>
                        <option value="3">Resigned</option>
                        <option value="4">Retired</option>
                        <option value="5">Terminated</option>
                        <option value="6">AWOL</option>
                        <option value="7">Deceased</option>
                        <option value="8">Dropped</option>
                        <option value="9">Labor</option>
                        <option value="10">Floating</option>
                        <option value="11">Rehired</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Location/ Department</label>
                        <select name="location" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="">All Locations</option>
                            @foreach ($locations ?? [] as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Sort By</label>
                        <select name="sort" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="last_name">Last Name</option>
                            <option value="hire_date">Date Hired</option>                                                                                   
                        </select>
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Direction</label>
                        <select name="direction" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>                                                                                   
                        </select>
                    </div>
                </div>

            <div id="customDateRange" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Hired From</label>
                    <input type="date" name="from_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Hired To</label>
                    <input type="date" name="to_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>
            </div>            

            <!-- Add format selection like employee report -->
            <div class="flex items-center space-x-4 mt-4">
                <div class="flex items-center">
                    <input type="radio" id="employee_pdf" name="format" value="pdf" checked
                        class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 focus:ring-gray-500">
                    <label for="employee_pdf" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">PDF</label>
                </div>
                <div class="flex items-center">
                    <input type="radio" id="employee_excel" name="format" value="excel"
                        class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 focus:ring-gray-500">
                    <label for="employee_excel" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Excel</label>
                </div>            
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t dark:border-gray-600">
                <button type="button" onclick="closeReportModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                    Generate Report
                </button>
            </div>
        </form>
    `
            },
            'supplies': {
                title: 'Supplies Summary Report',
                description: 'Select parameters for supplies summary',
                form: `
            <form id="reportForm" class="space-y-4 ml-1 mr-1">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Supplies Category</label>
                        <select name="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="">All Categories</option>
                            @foreach ($suppliesCategories ?? [] as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Status</label>
                        <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>                            
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Supplier</label>
                        <select name="supplier" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="">All Suppliers</option>
                            @foreach ($suppliers ?? [] as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Balance</label>
                        <select name="balance" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="balance">With Balance</option>
                            <option value="zero_balance">Zero Balance</option>
                            <option value="reorder"><= Reorder Qty</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Sort By</label>
                        <select name="sort" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="name">Name</option>
                            <option value="supplier_id">Supplier</option>
                            <option value="available_stock">Balance</option>
                        </select>
                    </div>
                </div>              
                
                <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t dark:border-gray-600">
                    <button type="button" onclick="closeReportModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                        Generate Report
                    </button>
                </div>
            </form>
        `
            },
            'supplies-receiving': {
                title: 'Supplies Receiving Report',
                description: 'Select parameters for supplies receiving report',
                form: `
        <form id="reportForm" class="space-y-4 ml-1 mr-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Period</label>
                    <select name="date_range" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Supplier</label>
                    <select name="supplier" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">All Suppliers</option>
                        @foreach ($suppliers ?? [] as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Received By</label>
                    <select name="employee" id="employee" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">All Employees</option>
                        @foreach ($employees ?? [] as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Report Type</label>
                    <select name="reptype" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="summary">Summary</option>
                        <option value="detailed">Detailed</option>                        
                    </select>
                </div>
            </div>

            <div id="customDateRange" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">From Date</label>
                    <input type="date" name="from_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">To Date</label>
                    <input type="date" name="to_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>
            </div>

            <!-- Add format selection like supplies receiving report -->
            <div class="flex items-center space-x-4 mt-4">
                <div class="flex items-center">
                    <input type="radio" id="supplies_receiving_pdf" name="format" value="pdf" checked
                        class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 focus:ring-gray-500">
                    <label for="supplies_receiving_pdf" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">PDF</label>
                </div>                
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t dark:border-gray-600">
                <button type="button" onclick="closeReportModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                    Generate Report
                </button>
            </div>
        </form>
    `
            },
            'supplies-issuance': {
                title: 'Supplies Issuance Report',
                description: 'Select parameters for supplies issuance report',
                form: `
        <form id="reportForm" class="space-y-4 ml-1 mr-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Date Range</label>
                    <select name="date_range" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Location</label>
                    <select name="location" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">All Locations</option>
                        @foreach ($locations ?? [] as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Issued To</label>
                    <select name="employee" id="employee" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">All Employees</option>
                        @foreach ($employees ?? [] as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Report Type</label>
                    <select name="reptype" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="summary">Summary</option>
                        <option value="detailed">Detailed</option>                        
                    </select>
                </div>
            </div>

            <div id="customDateRange" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">From Date</label>
                    <input type="date" name="from_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">To Date</label>
                    <input type="date" name="to_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>
            </div>            

            <!-- Add format selection like supplies receiving report -->
            <div class="flex items-center space-x-4 mt-4">
                <div class="flex items-center">
                    <input type="radio" id="supplies_issuance_pdf" name="format" value="pdf" checked
                        class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 focus:ring-gray-500">
                    <label for="supplies_issuance_pdf" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">PDF</label>
                </div>                
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t dark:border-gray-600">
                <button type="button" onclick="closeReportModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                    Generate Report
                </button>
            </div>
        </form>
    `
            },
            'duty-detail-order': {
                title: 'Duty Detail Order Report',
                description: 'Select location and date range to generate DDO report',
                form: `
                <form id="reportForm" class="space-y-4 ml-1 mr-1">
                    <div class="space-y-4">
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Select Location</label>
                            <select name="location" id="location" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" required>
                                <option value="">Choose a location...</option>
                                @foreach ($locations ?? [] as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">From Date</label>
                                <input type="date" name="from_date" value="{{ date('Y-m-d') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" 
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">To Date</label>
                                <input type="date" name="to_date" value="{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" 
                                    required>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="draft" name="draft" checked
                                    class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500">
                                <label for="draft" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Draft Report
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="radio" id="pdf" name="format" value="pdf" checked
                                    class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 focus:ring-gray-500">
                                <label for="pdf" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">PDF</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t dark:border-gray-600">
                        <button type="button" onclick="closeReportModal()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                            Generate Report
                        </button>
                    </div>
                </form>
            `
            },
            'budget-request': {
                title: 'Budget Request Report',
                description: 'Select parameters for budget request report',
                form: `
        <form id="reportForm" class="space-y-4 ml-1 mr-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Period</label>
                    <select name="date_range" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Location</label>
                    <select name="location" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">All locations</option>
                        @foreach ($locations ?? [] as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>                
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Requested By</label>
                    <select name="employee" id="employee" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">All Employees</option>
                        @foreach ($employees ?? [] as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->last_name }}, {{ $employee->first_name }} {{ $employee->middle_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Status</label>
                    <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="0">Pending</option>
                        <option value="1">Submitted</option>
                        <option value="2">Approved</option>
                        <option value="3">Rejected</option>
                        <option value="4">Void</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Report Type</label>
                    <select name="reptype" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="summary">Summary</option>
                        <option value="detailed">Detailed</option>                        
                    </select>
                </div>
            </div>    

            <div id="customDateRange" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">From Date</label>
                    <input type="date" name="from_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">To Date</label>
                    <input type="date" name="to_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>
            </div>

            <!-- Add format selection like budget request report -->
            <div class="flex items-center space-x-4 mt-4">
                <div class="flex items-center">
                    <input type="radio" id="budget_request_pdf" name="format" value="pdf" checked
                        class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 focus:ring-gray-500">
                    <label for="budget_request_pdf" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">PDF</label>
                </div>                
            </div>

            <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t dark:border-gray-600">
                <button type="button" onclick="closeReportModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-700 hover:bg-gray-800 rounded-lg focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700">
                    Generate Report
                </button>
            </div>
        </form>
    `
            },
        };

        document.addEventListener('DOMContentLoaded', function() {
            $(document).ready(function() {
                $('#asset_id').select2({
                    placeholder: "Select vehicle",
                    allowClear: true,
                    width: '100%'
                });

                $('#supplier').select2({
                    placeholder: "Select supplier",
                    allowClear: true,
                    width: '100%'
                });

                $('#employee').select2({
                    placeholder: "Select employee",
                    allowClear: true,
                    width: '100%'
                });

                $('#location').select2({
                    placeholder: "Select location",
                    allowClear: true,
                    width: '100%'
                });
            });
        });

        // Modal functions
        const allowedReports = @json($allowedReports);

        function openReportModal(reportType) {
            if (!allowedReports.includes(reportType)) return;
            const config = reportConfigs[reportType];
            if (!config) return;

            document.getElementById('modalTitle').textContent = config.title;
            document.getElementById('modalDescription').textContent = config.description;
            document.getElementById('formContainer').innerHTML = config.form;

            // Show modal
            document.getElementById('reportModal').classList.remove('hidden');
            document.getElementById('reportModal').classList.add('flex');

            // Add event listener for custom date range toggle if needed
            if (reportType === 'maintenance' || reportType === 'employee' || reportType === 'supplies-receiving' ||
                reportType === 'supplies-issuance' || reportType === 'asset-summary' || reportType === 'budget-request') {
                const dateRangeSelect = document.querySelector('select[name="date_range"]');
                if (dateRangeSelect) {
                    dateRangeSelect.addEventListener('change', function() {
                        const customRange = document.getElementById('customDateRange');
                        customRange.classList.toggle('hidden', this.value !== 'custom');
                    });
                }
            }

            // Add form submit handler
            const form = document.getElementById('reportForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    generateReport(reportType, new FormData(this));
                });
            }
        }

        function closeReportModal() {
            document.getElementById('reportModal').classList.add('hidden');
            document.getElementById('reportModal').classList.remove('flex');
            document.getElementById('formContainer').innerHTML = '';
        }

        let reportTimer = null;
        let reportBusy = false;


        const reportStages = [{
                at: 0,
                step: 0,
                text: 'Collecting data...'
            },
            {
                at: 5,
                step: 1,
                text: 'Building your report...'
            },
            {
                at: 15,
                step: 2,
                text: 'Finalizing the file. Large reports can take a while.'
            },
            {
                at: 40,
                step: 2,
                text: 'This is taking longer than usual, but the system is still processing your request.'
            },
        ];


        function showReportLoading(reportName = '', format = '') {
            const overlay = document.getElementById('reportLoading');
            const msg = document.getElementById('reportLoadingMsg');
            const time = document.getElementById('reportLoadingTime');
            const name = document.getElementById('reportLoadingName');
            let secs = 0;

            name.textContent = [reportName, format && `(${format})`].filter(Boolean).join(' ');
            time.textContent = '00:00';
            msg.textContent = reportStages[0].text;
            paintReportStep(0);

            overlay.classList.remove('hidden');
            overlay.classList.add('flex');

            reportTimer = setInterval(() => {
                secs++;
                const m = String(Math.floor(secs / 60)).padStart(2, '0');
                const s = String(secs % 60).padStart(2, '0');
                time.textContent = `${m}:${s}`;

                // pick the latest stage whose time has passed
                const stage = [...reportStages].reverse().find(st => secs >= st.at);
                if (stage && msg.textContent !== stage.text) {
                    msg.textContent = stage.text;
                    paintReportStep(stage.step);
                }
            }, 1000);
        }

        function hideReportLoading() {
            clearInterval(reportTimer);
            const overlay = document.getElementById('reportLoading');
            overlay.classList.add('hidden');
            overlay.classList.remove('flex');
        }

        function paintReportStep(activeStep) {
            document.querySelectorAll('#reportLoading .rl-step').forEach(li => {
                const idx = Number(li.dataset.step);
                const dot = li.querySelector('.rl-dot');
                dot.className = 'rl-dot h-2 w-2 rounded-full';
                li.classList.remove('text-gray-900', 'dark:text-white', 'font-medium');

                if (idx < activeStep) { // done
                    dot.classList.add('bg-green-500');
                } else if (idx === activeStep) { // active
                    dot.classList.add('bg-blue-600', 'rl-dot-active');
                    li.classList.add('text-gray-900', 'dark:text-white', 'font-medium');
                } else { // pending
                    dot.classList.add('bg-gray-300', 'dark:bg-gray-600');
                }
            });
        }

        const reportUrls = {
            'asset-summary': '/reports/asset-summary',
            'odometer': '/reports/odometer',
            'maintenance': '/reports/maintenance',
            'employee': '/reports/employee',
            'supplies': '/reports/supplies-summary',
            'supplies-receiving': '/reports/supplies-receiving',
            'supplies-issuance': '/reports/supplies-issuance',
            'duty-detail-order': '/reports/duty-detail-order',
            'budget-request': '/reports/budget-request',
        };

        async function generateReport(reportType, formData) {
            if (reportBusy) return;

            const base = reportUrls[reportType];
            if (!base) return;

            const params = Object.fromEntries(formData.entries());
            const url = `${base}?${new URLSearchParams(params).toString()}`;
            const format = (params.format || 'pdf').toLowerCase();
            const expectsDownload = ['excel', 'xlsx', 'csv'].includes(format);

            // Open the preview tab inside the click so popup blockers allow it
            let previewTab = null;
            if (!expectsDownload) {
                previewTab = window.open('', '_blank');
                if (previewTab) {
                    previewTab.document.write(
                        '<p style="font-family:Arial;padding:20px">Generating report, please wait...</p>');
                }
            }

            closeReportModal();
            reportBusy = true;
            showReportLoading(reportConfigs[reportType]?.title, format.toUpperCase());

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error(
                        response.status === 403 ? 'You are not allowed to generate this report.' :
                        response.status === 419 ? 'Your session has expired. Please refresh the page.' :
                        'The server returned an error (' + response.status + ').'
                    );
                }

                const contentType = (response.headers.get('Content-Type') || '').toLowerCase();

                // An HTML page here means a redirect (login, validation) instead of a file
                if (contentType.includes('text/html')) {
                    throw new Error('The server did not return a file. Please check your parameters or log in again.');
                }

                const blob = await response.blob();
                const blobUrl = URL.createObjectURL(blob);

                if (contentType.includes('pdf')) {
                    if (previewTab) {
                        previewTab.location.href = blobUrl;
                    } else {
                        window.open(blobUrl, '_blank');
                    }
                } else {
                    // Excel, CSV or anything else: download it
                    if (previewTab) previewTab.close();

                    const cd = response.headers.get('Content-Disposition') || '';
                    const match = cd.match(/filename\*?=(?:UTF-8'')?"?([^";]+)"?/i);
                    const ext = contentType.includes('csv') ? 'csv' : 'xlsx';
                    const filename = match ? decodeURIComponent(match[1]) : `report.${ext}`;

                    const a = document.createElement('a');
                    a.href = blobUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                }

                setTimeout(() => URL.revokeObjectURL(blobUrl), 60000);
            } catch (err) {
                if (previewTab) previewTab.close();
                alert('Failed to generate the report. ' + err.message);
            } finally {
                reportBusy = false;
                hideReportLoading();
            }
        }



        // function generateReport(reportType, formData) {
        //     // Convert FormData to object
        //     const params = Object.fromEntries(formData.entries());

        //     // Build query string
        //     const queryString = new URLSearchParams(params).toString();

        //     //alert('Report parameters: ' + queryString); // For debugging

        //     // Generate report URL based on type
        //     let url;
        //     switch (reportType) {
        //         case 'asset-summary':
        //             url = `/reports/asset-summary?${queryString}`;
        //             break;
        //         case 'odometer':
        //             url = `/reports/odometer?${queryString}`;
        //             break;
        //         case 'maintenance':
        //             url = `/reports/maintenance?${queryString}`;
        //             break;
        //         case 'employee':
        //             url = `/reports/employee?${queryString}`;
        //             break;
        //         case 'supplies':
        //             url = `/reports/supplies-summary?${queryString}`;
        //             break;
        //         case 'supplies-receiving':
        //             url = `/reports/supplies-receiving?${queryString}`;
        //             break;
        //         case 'supplies-issuance':
        //             url = `/reports/supplies-issuance?${queryString}`;
        //             break;
        //         case 'duty-detail-order':
        //             url = `/reports/duty-detail-order?${queryString}`;
        //             break;
        //         case 'budget-request':
        //             url = `/reports/budget-request?${queryString}`;
        //     }

        //     // Open in new tab for PDF preview
        //     if (params.format === 'excel') {
        //         window.location.href = url; // Download Excel
        //     } else {
        //         window.open(url, '_blank'); // Preview PDF
        //     }

        //     closeReportModal();
        // }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('reportModal');
            if (event.target === modal) {
                closeReportModal();
            }
        }
    </script>
@endsection
