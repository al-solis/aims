@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Asset Transfer Management</h1>
                <p class="text-sm text-gray-500">
                    Manage Asset Transfers
                </p>
            </div>
            <div class="flex items-center gap-2 mt-0">
                <a href="{{ route('asset.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>

                @if ($assets->canBeTransferred())
                    <button data-modal-target="add-modal" data-modal-toggle="add-modal"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Transfer Asset
                    </button>
                @else
                    <button disabled
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-600 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Transfer Asset
                    </button>
                @endif
            </div>
        </div>

        {{-- Stats Cards --}}
        @php
            $cards = [
                [
                    'title' => 'Total Transfers',
                    'value' => $totalTransfers,
                    'color' => 'blue',
                    'icon' => '
                        <svg xmlns="http://www.w3.org/2000/svg" class = "w-5 h-5 text-blue-600" width="16" height="16" fill="currentColor" class="bi bi-credit-card-2-front" viewBox="0 0 16 16">
                        <path
                                                d="m11.798 8.271-3.182 1.97c-.27.166-.616-.036-.616-.372V9.1s-2.571-.3-4 2.4c.571-4.8 3.143-4.8 4-4.8v-.769c0-.336.346-.538.616-.371l3.182 1.969c.27.166.27.576 0 .742" />
                                            <path
                                                d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14h10.348a2 2 0 0 0 1.991-1.819l.637-7A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m.694 2.09A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09l-.636 7a1 1 0 0 1-.996.91H2.826a1 1 0 0 1-.995-.91zM6.172 2a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z" />
                                        </svg>',
                ],
                [
                    'title' => 'Active',
                    'value' => $activeTransfers,
                    'color' => 'green',
                    'icon' => '
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 13l4 4L19 7" />
                        </svg>',
                ],
                [
                    'title' => 'Cancelled',
                    'value' => $cancelledTransfers,
                    'color' => 'red',
                    'icon' => '
                        <svg class="w-5 h-5 text-red-600" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                        </svg>',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($cards as $card)
                <div class="bg-white border rounded-xl p-4 flex items-center gap-4">
                    <div
                        class="w-10 h-10 rounded-lg bg-{{ $card['color'] }}-100 
                                flex items-center justify-center">
                        {!! $card['icon'] !!}
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">{{ $card['title'] }}</p>
                        <p class="text-xl font-semibold text-gray-900">
                            {{ $card['value'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 pt-1">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 pt-1"
                data-success="true">
                {{ session('success') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Clear form fields after successful submission
                    clearModalFields();
                });
            </script>
        @endif

        {{-- Filters --}}
        <form action="" method="GET">
            <div class="flex flex-col md:flex-row gap-2 text-xs md:text-sm">
                <div class="md:w-2/3 w-full">
                    <input type="text" id="simple-search" name="search" placeholder="Search by name or description..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        value = "{{ request()->query('search') }}" oninput="this.form.submit()">
                </div>

                <div class="md:w-1/3 w-full">
                    <select id="status" name="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Active</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
            <button type="submit"
                class="hidden mt-4 w-full shrink-0 rounded-lg bg-gray-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 sm:mt-0 sm:w-auto">Search</button>
        </form>

        {{-- Table --}}
        <input type="hidden" name="last_transfer_no" id="last_transfer_no">
        <div class="bg-white border rounded-xl overflow-x-auto md:overflow-visible scroll-smooth">
            <table class="min-w-full text-xs">
                <thead class="bg-gray-200 text-gray-600">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left w-[120px]">Code</th>
                        <th scope="col" class="px-4 py-3 text-left w-[80px]">Date</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Asset</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Description</th>
                        <th scope="col" class="px-4 py-3 text-left w-[200px]">Note</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">From</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">To</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Location</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Sub-location</th>
                        <th scope="col" class="px-4 py-3 text-left w-[80px]">Cancelled</th>
                        <th scope="col" class="px-4 py-3 text-center w-[150px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y text-xs">
                    @forelse($transfers as $transfer)
                        @foreach ($transfer->transferDetails as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 w-[120px]">{{ $transfer->code }}</td>
                                <td class="px-4 py-3 w-[80px]">{{ $transfer->date }}</td>
                                <td class="px-4 py-3 w-[150px]">{{ $detail->asset->asset_code ?? '' }}</td>
                                <td class="px-4 py-3 w-[150px]">{{ $detail->asset->name ?? '' }}</td>
                                <td class="px-4 py-3 w-[200px]">{{ $transfer->description }}</td>
                                <td class="px-4 py-3 w-[150px]">
                                    {{ $detail->fromEmployee->first_name ?? '' }}
                                    {{ $detail->fromEmployee->last_name ?? '' }}</td>
                                <td class="px-4 py-3 w-[150px]">{{ $detail->toEmployee->first_name ?? '' }}
                                    {{ $detail->toEmployee->last_name ?? '' }}</td>
                                <td class="px-4 py-3 w-[150px]">{{ $detail->toLocation->name ?? '' }}</td>
                                <td class="px-4 py-3 w-[150px]">{{ $detail->toSublocation->name ?? '' }}
                                </td>
                                <td class="px-4 py-3 w-[100px] text-xs font-semibold">
                                    @php
                                        $statuses = [
                                            1 => ['color' => 'bg-red-100 text-red-600', 'label' => 'Cancelled'],
                                            0 => ['color' => 'bg-green-100 text-green-700', 'label' => 'Active'],
                                        ];
                                        $status = $statuses[$transfer->cancelled] ?? [
                                            'color' => 'bg-gray-100 text-gray-600',
                                            'label' => 'Unknown',
                                        ];
                                    @endphp

                                    <span class="px-2 py-1 text-xs rounded-full {{ $status['color'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 w-[150px]">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="button" title="Edit transfer {{ $transfer->code }}"
                                            data-modal-target="edit-modal" data-modal-toggle="edit-modal"
                                            data-id="{{ $transfer->id }}" data-code="{{ $transfer->code }}"
                                            data-date="{{ Carbon::parse($transfer->date)->format('Y-m-d') }}"
                                            data-description="{{ $transfer->transferDetails->first()->asset->name ?? '' }}"
                                            data-note="{{ $transfer->description }}"
                                            data-from_employee="{{ $transfer->transferDetails->first()->from_employee_id ?? '' }}"
                                            data-from_employee_name="{{ $transfer->transferDetails->first()->fromEmployee ? $transfer->transferDetails->first()->fromEmployee->last_name . ', ' . $transfer->transferDetails->first()->fromEmployee->first_name . ' ' . $transfer->transferDetails->first()->fromEmployee->middle_name : 'N/A' }}"
                                            data-from_location = "{{ $transfer->transferDetails->first()->from_location_id ?? '' }}"
                                            data-from_sublocation = "{{ $transfer->transferDetails->first()->from_subloc_id ?? '' }}"
                                            data-to_employee="{{ $transfer->transferDetails->first()->to_employee_id ?? '' }}"
                                            data-to_location="{{ $transfer->transferDetails->first()->to_location_id ?? '' }}"
                                            data-to_sublocation = "{{ $transfer->transferDetails->first()->to_subloc_id ?? '' }}"
                                            data-status="{{ $transfer->cancelled }}" onclick="openEditModal(this)"
                                            class="group flex space-x-5 text-gray-500 hover:text-blue-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path
                                                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                <path fill-rule="evenodd"
                                                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                            </svg>
                                        </button>

                                        <a href="{{ route('transfer.print', $transfer->id) }}" type="button"
                                            target="_blank" title="Print transfer : {{ $transfer->code }}"
                                            class="group flex space-x-1 text-gray-500 hover:text-yellow-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                                                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                                                <path
                                                    d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                                            </svg>
                                        </a>

                                        <button type="button" title="Void transfer {{ $transfer->code }}"
                                            data-id="{{ $transfer->id }}" data-name="{{ $transfer->name }}"
                                            data-asset_status="{{ $assets->status ?? '' }}"
                                            data-code="{{ $transfer->code }}"
                                            data-description="{{ $transfer->description }}"
                                            data-cancelled="{{ $transfer->cancelled }}" onclick="voidTransfer(this)"
                                            class="void-btn hidden group flex text-gray-500 hover:text-red-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                <path
                                                    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                <path
                                                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                            </svg>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No Asset Transfers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div
            class="w-full md:w-auto text-xs flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 mb-2">
            {{ $transfers->links() }}
        </div>
    </div>

    <!-- Create Transfer modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-2xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-2 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                        Create Transfer
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="add-modal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="overflow-y-auto max-h-[70vh]">
                    <form action="{{ route('transfer.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="purchase_date" id="purchase_date"
                            value="{{ $assets->purchase_date ?? '' }}">
                        <div class="grid ml-1 mr-1 gap-2 mb-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <label for="transfer_date"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Date*</label>
                                <input type="date" name="transfer_date" id="transfer_date" max="{{ date('Y-m-d') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="mm/dd/yyyy" required>
                            </div>
                            <div class="sm:col-span-1">
                                <input type="hidden" name="asset_id" id="asset_id" value="{{ $assets->id ?? '' }}">
                                <label for="asset_code"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Asset
                                    ID</label>
                                <input type="text" name="asset_code" id="asset_code"
                                    value="{{ $assets->asset_code ?? '' }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. FA-2026-00000" readonly>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="asset_description"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Description</label>
                                <input type="text" name="asset_description" id="asset_description"
                                    value="{{ $assets->name ?? '' }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Computer Laptop" readonly>
                            </div>
                            <input type="hidden" name="from_employee_id" id="from_employee_id"
                                value="{{ $assets->assigned_to }}">
                            <input type="hidden" name="from_location_id" id="from_location_id"
                                value="{{ $assets->location_id }}">
                            <input type="hidden" name="from_sublocation_id" id="from_sublocation_id"
                                value="{{ $assets->sublocation_id }}">
                            <div class="sm:col-span-1">
                                <label for="from_employee_name"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">From</label>
                                <input type="text" name="from_employee_name" id="from_employee_name"
                                    value="{{ $assets->assigned_to ? $assets->assigned_user->last_name . ', ' . $assets->assigned_user->first_name . ' ' . $assets->assigned_user->middle_name : 'N/A' }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Computer Laptop" readonly>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="to_employee_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Transfer to*</label>
                                <select id="to_employee_id" name="to_employee_id"
                                    class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value="" selected>Select employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">
                                            {{ $employee->last_name }}, {{ $employee->first_name }}
                                            {{ $employee->middle_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="location_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Location</label>
                                <select id="location_id" name="location_id" data-target="#sublocation_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                    <option value="" selected>Select location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">
                                            {{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="sublocation_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Sub-Location</label>
                                <select id="sublocation_id" name="sublocation_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                    <option value="" selected>Select sub-location</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="description"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Note</label>
                                <textarea id="description" name="description" rows="2"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Transfer note"></textarea>
                            </div>

                        </div>

                        <button type="submit"
                            class="text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-xs px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            <svg class="mr-1 -ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Create Transfer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End create ID Type modal -->

    <!-- Modal  Edit-->
    <div id="edit-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Update Transfer
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="edit-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="overflow-y-auto max-h-[70vh]">
                    <form id="editForm" class="p-4 md:p-5" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="edit_id" id="edit_id">

                        <input type="hidden" name="edit_purchase_date" id="edit_purchase_date"
                            value="{{ $transfer->date ?? '' }}">
                        <div class="grid ml-1 mr-1 gap-2 mb-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <label for="edit_transfer_date"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Date*</label>
                                <input type="date" name="edit_transfer_date" id="edit_transfer_date"
                                    max="{{ date('Y-m-d') }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="mm/dd/yyyy" required>
                            </div>
                            <div class="sm:col-span-1">
                                <input type="hidden" name="edit_asset_id" id="edit_asset_id">
                                <label for="edit_asset_code"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Asset
                                    ID</label>
                                <input type="text" name="edit_asset_code" id="edit_asset_code"
                                    value="{{ $assets->asset_code ?? '' }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. FA-2026-00000" readonly>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="edit_description"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Description</label>
                                <input type="text" name="edit_description" id="edit_description"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Computer Laptop" readonly>
                            </div>
                            <input type="hidden" name="edit_from_employee_id" id="edit_from_employee_id">
                            <input type="hidden" name="edit_from_location_id" id="edit_from_location_id">
                            <input type="hidden" name="edit_from_sublocation_id" id="edit_from_sublocation_id">
                            <div class="sm:col-span-1">
                                <label for="edit_from_employee_name"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">From</label>
                                <input type="text" name="edit_from_employee_name" id="edit_from_employee_name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    readonly>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="edit_to_employee_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Transfer to*</label>
                                <select id="edit_to_employee_id" name="edit_to_employee_id"
                                    class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    required>
                                    <option value="" selected>Select employee</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">
                                            {{ $employee->last_name }}, {{ $employee->first_name }}
                                            {{ $employee->middle_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_location_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Location</label>
                                <select id="edit_location_id" name="edit_location_id" data-target="#edit_sublocation_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                    <option value="" selected>Select location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">
                                            {{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-1">
                                <label for="edit_sublocation_id"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Sub-Location</label>
                                <select id="edit_sublocation_id" name="edit_sublocation_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                    <option value="" selected>Select sub-location</option>
                                    @foreach ($sublocation as $subloc)
                                        <option value="{{ $subloc->id }}">
                                            {{ $subloc->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="edit_note"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Description</label>
                                <textarea id="edit_note" name="edit_note" rows="2"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Transfer note"></textarea>
                            </div>
                        </div>

                        @if ($assets->canBeTransferred())
                            <button type="submit" id="updateBtn"
                                class="mt-2 text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-xs px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                Update transfer
                            </button>

                            <div id="cancelledMsg"
                                class="mt-2 text-white inline-flex items-center bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-md text-xs px-5 py-2.5 text-center">
                                <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                This transfer is cancelled
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End edit modal -->

    <script>
        $(document).ready(function() {
            $('#to_employee_id').select2({
                placeholder: "Select employee",
                allowClear: true,
                width: '100%'
            });

            $('#edit_to_employee_id').select2({
                placeholder: "Select employee",
                allowClear: true,
                width: '100%'
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            //Check the last transfer code
            const assetId = document.getElementById('asset_id').value;
            $.ajax({
                url: `/get-last-transfer-code/${assetId}`,
                type: 'GET',
                success: function(response) {

                    const lastTransferNo = response.last_code;

                    document.querySelectorAll('.void-btn').forEach(button => {
                        const transferCode = button.getAttribute('data-code');
                        const cancelled = button.getAttribute('data-cancelled');

                        if (transferCode === lastTransferNo && cancelled === '0') {
                            button.classList.remove('hidden');
                        }
                    });
                },
                error: function() {
                    console.error('Failed to fetch last transfer code');
                }
            });
        });

        function clearModalFields() {
            // Clear all form fields
            const form = document.querySelector('form');
            form.reset();

            // Remove any success messages after a delay
            setTimeout(() => {
                const successMessage = document.querySelector('[data-success]');
                if (successMessage) {
                    successMessage.remove();
                }
            }, 3000);
        }

        function openEditModal(button) {
            const id = button.getAttribute('data-id');
            const status = button.getAttribute('data-status');
            document.getElementById('edit_id').value = id;

            document.getElementById('edit_description').value = button.getAttribute('data-description');
            document.getElementById('edit_transfer_date').value = button.getAttribute('data-date');
            document.getElementById('edit_note').value = button.getAttribute('data-note');

            document.getElementById('edit_from_employee_id').value = button.getAttribute('data-from_employee');
            document.getElementById('edit_from_employee_name').value = button.getAttribute('data-from_employee_name');
            document.getElementById('edit_from_location_id').value = button.getAttribute('data-from_location');
            document.getElementById('edit_from_sublocation_id').value = button.getAttribute('data-from_sublocation');

            document.getElementById('edit_to_employee_id').value = button.getAttribute('data-to_employee');
            document.getElementById('edit_location_id').value = button.getAttribute('data-to_location');
            selectedEditSublocation = button.getAttribute('data-to_sublocation');
            document.getElementById('edit_sublocation_id').value = selectedEditSublocation;

            $('#edit_to_employee_id')
                .val(button.getAttribute('data-to_employee'))
                .trigger('change');

            document.getElementById('edit_location_id').value = button.getAttribute('data-to_location');
            selectedEditSublocation = button.getAttribute('data-to_sublocation');

            $('#edit_location_id').trigger('change');

            const updateBtn = document.getElementById('updateBtn');
            const cancelledMessage = document.getElementById('cancelledMsg');

            if (status == '0') {
                updateBtn.classList.remove('hidden');
                cancelledMessage.classList.add('hidden');
            } else {
                updateBtn.classList.add('hidden');
                cancelledMessage.classList.remove('hidden');
            }

            const form = document.getElementById('editForm');
            form.action = `/asset/transfer/update/${id}`;
        }

        let selectedEditSublocation = null;

        // ADD + EDIT location change handler
        $(document).on('change', '#location_id, #edit_location_id', function() {

            const locationId = $(this).val();
            const sublocationSelect = $($(this).data('target'));

            // alert('Location Id: ' + locationId);
            // alert('Sublocation Select Id: ' + sublocationSelect.attr('id'));

            sublocationSelect
                .html('<option>Loading...</option>')
                .prop('disabled', true);

            if (!locationId) {
                sublocationSelect.html('<option value="">Select sub-location</option>');
                return;
            }

            $.ajax({
                url: `/get-sublocations/${locationId}`,
                type: 'GET',
                success: function(data) {
                    sublocationSelect.empty()
                        .append('<option value="">Select sub-location</option>');

                    $.each(data, function(_, sublocation) {
                        sublocationSelect.append(
                            `<option value="${sublocation.id}">${sublocation.name}</option>`
                        );
                    });

                    if (selectedEditSublocation && sublocationSelect.attr('id') ===
                        'edit_sublocation_id') {
                        sublocationSelect.val(selectedEditSublocation);
                        selectedEditSublocation = null;
                    }

                    sublocationSelect.prop('disabled', false);
                }
            });
        });

        document.getElementById('to_employee_id').addEventListener('change', function() {
            const from_employee_id = document.getElementById('from_employee_id').value;
            if (this.value == from_employee_id) {
                alert(
                    'The "Destination" employee cannot be the same as the "Source" employee. Please select a different employee.'
                );
                this.value = '';
                this.focus();
            }
        });

        document.getElementById('transfer_date').addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const purchaseDate = document.getElementById('purchase_date').value;
            if (selectedDate < new Date(purchaseDate)) {
                alert(
                    `The transfer date cannot be before the purchase date ${purchaseDate}. Please select a valid date.`
                );
                this.value = '';
                this.focus();
            }

            $.ajax({
                url: `/validate-transfer-date?asset_id=${document.getElementById('asset_id').value}&transfer_date=${this.value}`,
                type: 'GET',
                success: function(response) {
                    if (!response.valid) {
                        alert(response.message);
                        document.getElementById('transfer_date').value = '';
                        document.getElementById('transfer_date').focus();
                    }
                }
            });
        });

        function voidTransfer(button) {
            const transferId = button.getAttribute('data-id');
            const transferCode = button.getAttribute('data-code');
            const assetStatus = button.getAttribute('data-asset_status');

            if (assetStatus === '5') {
                alert('This transfer cannot be voided because the asset has already been retired.');
                return;
            }

            if (confirm(`Are you sure you want to void transfer ${transferCode}? This action cannot be undone.`)) {
                $.ajax({
                    url: `/transfer/${transferId}/void`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert('An error occurred while trying to void the transfer. Please try again.');
                    }
                });
            }
        }
    </script>
@endsection
