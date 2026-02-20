@extends('dashboard')
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Reports</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Select a report to generate</p>
        </div>

        <!-- Reports Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Asset Reports Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <span
                            class="text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded">Asset</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Asset Summary Report</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Generate summary of all assets with their
                        current status and location</p>
                    <button onclick="openReportModal('asset-summary')"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Odometer Readings Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span
                            class="text-xs font-medium text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900 px-2 py-1 rounded">Odometer</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Odometer Readings Report</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Track vehicle usage with detailed odometer
                        readings by date range</p>
                    <button onclick="openReportModal('odometer')"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Maintenance Reports Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span
                            class="text-xs font-medium text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900 px-2 py-1 rounded">Maintenance</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Maintenance History</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">View maintenance records with cost analysis and
                        schedules</p>
                    <button onclick="openReportModal('maintenance')"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Employee Reports Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-600 dark:text-orange-300"
                                fill="none" width="16" height="16" stroke="currentColor" class="bi bi-people-fill"
                                viewBox="0 0 16 16">
                                <path
                                    d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                            </svg>
                        </div>
                        <span
                            class="text-xs font-medium text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900 px-2 py-1 rounded">Employee</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Employee List</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Generate employee listing per location, status
                        and date range.
                    </p>
                    <button onclick="openReportModal('employee')"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Supplies Reports Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-600 dark:text-purple-300"
                                fill="none" width="16" height="16" stroke="currentColor"
                                class="bi bi-people-fill" viewBox="0 0 16 16">
                                <path
                                    d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                            </svg>
                        </div>
                        <span
                            class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900 px-2 py-1 rounded">Supplies</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Supplies Summary Report</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Generate supplies balance summary with
                        filtering by category and supplier.
                    </p>
                    <button onclick="openReportModal('supplies')"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Supplies Receiving Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-600 dark:text-indigo-300"
                                fill="none" width="16" height="16" stroke="currentColor"
                                class="bi bi-receipt" viewBox="0 0 16 16">
                                <path
                                    d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0z" />
                                <path
                                    d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5" />
                            </svg>
                        </div>
                        <span
                            class="text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900 px-2 py-1 rounded">Supplies
                            Receiving</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Supplies Receiving Report</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Generate detailed and summary supplies
                        receiving report. Filtered by supplier and date range.
                    </p>
                    <button onclick="openReportModal('supplies-receiving')"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Supplies Issuance Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600 dark:text-red-300"
                                fill="none" width="16" height="16" stroke="currentColor"
                                class="bi bi-folder-symlink" viewBox="0 0 16 16">
                                <path
                                    d="m11.798 8.271-3.182 1.97c-.27.166-.616-.036-.616-.372V9.1s-2.571-.3-4 2.4c.571-4.8 3.143-4.8 4-4.8v-.769c0-.336.346-.538.616-.371l3.182 1.969c.27.166.27.576 0 .742" />
                                <path
                                    d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14h10.348a2 2 0 0 0 1.991-1.819l.637-7A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m.694 2.09A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09l-.636 7a1 1 0 0 1-.996.91H2.826a1 1 0 0 1-.995-.91zM6.172 2a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z" />
                            </svg>
                        </div>
                        <span
                            class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900 px-2 py-1 rounded">Supplies
                            Issuance</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Supplies Issuance Report</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Generate detailed and summary supplies
                        issuance report. Filtered by location, date range and status.
                    </p>
                    <button onclick="openReportModal('supplies-issuance')"
                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>

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

    <script>
        // Report configurations
        const reportConfigs = {
            'asset-summary': {
                title: 'Asset Summary Report',
                description: 'Select parameters for asset summary',
                form: `
            <form id="reportForm" class="space-y-4 ml-1 mr-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Date Range</label>
                    <select name="date_range" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Employee Status</label>
                    <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="0">Inactive</option>
                        <option value="1">Active</option>
                        <option value="2">On Leave</option>
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
            }
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
        function openReportModal(reportType) {
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
                reportType === 'supplies-issuance') {
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

        function generateReport(reportType, formData) {
            // Convert FormData to object
            const params = Object.fromEntries(formData.entries());

            // Build query string
            const queryString = new URLSearchParams(params).toString();

            //alert('Report parameters: ' + queryString); // For debugging

            // Generate report URL based on type
            let url;
            switch (reportType) {
                case 'asset-summary':
                    url = `/reports/asset-summary?${queryString}`;
                    break;
                case 'odometer':
                    url = `/reports/odometer?${queryString}`;
                    break;
                case 'maintenance':
                    url = `/reports/maintenance?${queryString}`;
                    break;
                case 'employee':
                    url = `/reports/employee?${queryString}`;
                    break;
                case 'supplies':
                    url = `/reports/supplies-summary?${queryString}`;
                    break;
                case 'supplies-receiving':
                    url = `/reports/supplies-receiving?${queryString}`;
                    break;
                case 'supplies-issuance':
                    url = `/reports/supplies-issuance?${queryString}`;
                    break;
            }

            // Open in new tab for PDF preview
            if (params.format === 'excel') {
                window.location.href = url; // Download Excel
            } else {
                window.open(url, '_blank'); // Preview PDF
            }

            closeReportModal();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('reportModal');
            if (event.target === modal) {
                closeReportModal();
            }
        }
    </script>
@endsection
