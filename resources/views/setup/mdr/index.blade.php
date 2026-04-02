@extends('dashboard')
@section('content')
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">MDR Location Setup</h1>
                <p class="text-sm text-gray-500">
                    Manage employees and reliever for MDR locations.
                </p>
            </div>
            <div class="flex items-center gap-2 mt-0">
                <a href="{{ route('setup.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>

                <a href="{{ route('mdr.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Setup for Location
                </a>

                <button data-modal-target="copy-modal" data-modal-toggle="copy-modal"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-copy" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                    </svg>
                    Copy Setup
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        @php
            $cards = [
                [
                    'title' => 'Total Setups',
                    'value' => $totalSetups,
                    'color' => 'blue',
                    'icon' => '
                        <svg xmlns="http://www.w3.org/2000/svg" class = "w-5 h-5 text-blue-600" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                        <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/>
                        <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                        </svg>',
                ],
                [
                    'title' => 'Active Setups',
                    'value' => $activeSetups,
                    'color' => 'green',
                    'icon' => '
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 13l4 4L19 7" />
                        </svg>',
                ],
                [
                    'title' => 'Total Inactive',
                    'value' => $totalInactiveSetups,
                    'color' => 'orange',
                    'icon' => '
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7h18M3 12h18M3 17h18" />
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
                    <input type="text" id="simple-search" name="search"
                        placeholder="Search by name, code, or description..."
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        value = "{{ request()->query('search') }}" oninput="this.form.submit()">
                </div>

                <div class="md:w-1/3 w-full">
                    <select id="status" name="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                        onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit"
                class="hidden mt-4 w-full shrink-0 rounded-lg bg-gray-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800 sm:mt-0 sm:w-auto">Search</button>
        </form>

        {{-- Table --}}
        <div class="bg-white border rounded-xl overflow-x-auto md:overflow-visible scroll-smooth">
            <table class="min-w-full text-xs">
                <thead class="bg-gray-200 text-gray-600">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Code</th>
                        <th scope="col" class="px-4 py-3 text-left w-[200px]">Location</th>
                        <th scope="col" class="px-4 py-3 text-left w-[300px]">Remarks</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Count</th>
                        <th scope="col" class="px-4 py-3 text-left w-[150px]">Status</th>
                        <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($setups as $setup)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium w-[100px]">{{ $setup->id }}</td>
                            <td class="px-4 py-3 w-[200px]">{{ $setup->location->name }}</td>
                            <td class="px-4 py-3 w-[300px]">{{ $setup->remarks }}</td>
                            <td class="px-4 py-3 w-[100px]">{{ $setup->mdrDetails->count() }}</td>
                            <td class="px-4 py-3 w-[150px] text-xs font-semibold">
                                @php
                                    $statuses = [
                                        0 => ['color' => 'bg-red-100 text-red-600', 'label' => 'Inactive'],
                                        1 => ['color' => 'bg-green-100 text-green-700', 'label' => 'Active'],
                                    ];
                                    $status = $statuses[$setup->status] ?? [
                                        'color' => 'bg-gray-100 text-gray-600',
                                        'label' => 'Unknown',
                                    ];
                                @endphp

                                <span class="px-2 py-1 text-xs rounded-full {{ $status['color'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 w-[50px]">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('mdr.edit', $setup->id) }}"
                                        title="Edit location {{ $setup->location->name }}" data-id="{{ $setup->id }}"
                                        data-status="{{ $setup->status }}"
                                        class="group flex space-x-1 text-gray-500 hover:text-blue-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path
                                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                            <path fill-rule="evenodd"
                                                d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                        </svg>
                                        {{-- <span class="hidden group-hover:inline transition-opacity duration-200"></span> --}}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No MDR setup found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination Links -->
        <div
            class="w-full md:w-auto text-xs flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 mb-2">
            {{ $setups->links() }}
        </div>
    </div>

    <!-- Copy location modal -->
    <div id="copy-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-xl h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-md shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-2 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                        Copy Setup
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="copy-modal">
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
                    <form id="copyForm">
                        @csrf
                        <div class="grid ml-1 mr-1 gap-2 mb-4 sm:grid-cols-1">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="md:col-span-1">
                                    <label for="source_id"
                                        class="mb-1 block text-xs font-medium text-gray-900 dark:text-white">Source
                                        Location*</label>
                                    <select type="text" name="source_id" id="source_id"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        required>
                                        <option value="" selected disabled>Select source location</option>
                                        @foreach ($setups as $setup)
                                            <option value="{{ $setup->location_id }}">{{ $setup->location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-1">
                                    <label for="destination_id"
                                        class="mb-1 block text-xs font-medium text-gray-900 dark:text-white">Destination
                                        Location*</label>
                                    <select type="text" name="destination_id" id="destination_id"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                        required>
                                        <option value="" selected disabled>Select destination location</option>
                                        @foreach ($destination as $loc)
                                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                        <button type="button" onclick="copySetup(this)"
                            class="text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-xs px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-copy mr-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                            </svg>
                            Copy Setup
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End copy location modal -->

    <script>
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

        // function openEditModal(button) {
        //     const id = button.getAttribute('data-id');
        //     document.getElementById('edit_id').value = button.getAttribute('data-id');
        //     document.getElementById('edit_code').value = button.getAttribute('data-code');
        //     document.getElementById('edit_name').value = button.getAttribute('data-name');
        //     document.getElementById('edit_description').value = button.getAttribute('data-description');
        //     document.getElementById('edit_address').value = button.getAttribute('data-address');
        //     document.getElementById('edit_contact_number').value = button.getAttribute('data-contact');
        //     document.getElementById('edit_status').value = button.getAttribute('data-status');

        //     const form = document.getElementById('editForm');
        //     form.action = `location/${id}`;
        // }
        function copySetup(button) {
            const sourceSelect = document.getElementById('source_id');
            const destinationSelect = document.getElementById('destination_id');

            const sourceId = sourceSelect.value;
            const sourceName = sourceSelect.options[sourceSelect.selectedIndex].text;
            const destinationId = destinationSelect.value;
            const destinationName = destinationSelect.options[destinationSelect.selectedIndex].text;

            if (!sourceId || !destinationId) {
                alert('Please select both source and destination locations.');
                return;
            }

            if (sourceId === destinationId) {
                alert('Source and destination locations must be different.');
                return;
            }

            if (!confirm('Are you sure you want to copy the setup from ' + sourceName + ' to ' + destinationName + '?')) {
                return;
            }

            // Disable the button to prevent double submission
            const copyButton = button;
            copyButton.disabled = true;
            copyButton.textContent = 'Copying...';


            $.ajax({
                url: '{{ route('mdr.location.copy') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    source_id: sourceId,
                    destination_id: destinationId
                },
                success: function(response) {
                    alert(response.success || 'Setup copied successfully.');

                    // Reset the form/selects
                    if (typeof clearModalFields === 'function') {
                        clearModalFields();
                    }

                    location.reload();
                },
                error: function(xhr, status, error) {
                    let errorMessage = 'Error copying setup.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    alert(errorMessage);
                },
                complete: function() {
                    copyButton.disabled = false;
                    copyButton.textContent = 'Copy Setup';
                }
            });
        }
    </script>
@endsection
