@extends('dashboard')
@section('content')
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Location Management</h1>
                <p class="text-sm text-gray-500">
                    Manage locations and sub-locations for asset tracking
                </p>
            </div>

            <button data-modal-target="add-modal" data-modal-toggle="add-modal"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Location
            </button>
        </div>

        {{-- Stats Cards --}}
        @php
            $cards = [
                [
                    'title' => 'Total Locations',
                    'value' => $totalLocations,
                    'color' => 'blue',
                    'icon' => '
                        <svg xmlns="http://www.w3.org/2000/svg" class = "text-blue-600" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                        <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/>
                        <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                        </svg>',
                ],
                [
                    'title' => 'Active Locations',
                    'value' => $activeLocations,
                    'color' => 'green',
                    'icon' => '
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 13l4 4L19 7" />
                        </svg>',
                ],
                [
                    'title' => 'Total Sub-Locations',
                    'value' => $totalSubLocations,
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
        <div class="flex flex-col md:flex-row gap-4 text-xs md:text-sm">
            <div class="md:w-2/3 w-full">
                <input type="text" placeholder="Search by name, code, or city..."
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
            </div>

            <div class="md:w-1/3 w-full">
                <select id="status" name="status"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                    {{-- <option selected="">Select product type</option> --}}
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                    <option value="2">Under Maintenance</option>
                </select>
            </div>

        </div>

        {{-- Table --}}
        <div class="bg-white border rounded-xl overflow-hidden">
            <table class="min-w-full text-xs">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Code</th>
                        <th scope="col" class="px-4 py-3 text-left w-[200px]">Name</th>
                        <th scope="col" class="px-4 py-3 text-left w-[350px]">Description</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Sub-Locations</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Status</th>
                        <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($locations as $location)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium w-[100px]">{{ $location->code }}</td>
                            <td class="px-4 py-3 w-[200px]">{{ $location->name }}</td>
                            <td class="px-4 py-3 w-[350px]">{{ $location->description }}</td>
                            <td class="px-4 py-3 w-[100px]">
                                <a href="#" class="text-gray-600 hover:underline">
                                    {{ $location->sub_locations_count }} sub-locations
                                </a>
                            </td>
                            <td class="px-4 py-3 w-[100px]">
                                @php
                                    $statuses = [
                                        0 => ['color' => 'bg-gray-100 text-gray-600', 'label' => 'Inactive'],
                                        1 => ['color' => 'bg-green-100 text-green-700', 'label' => 'Active'],
                                        2 => ['color' => 'bg-red-100 text-red-700', 'label' => 'Under Maintenance'],
                                    ];
                                    $status = $statuses[$location->status] ?? [
                                        'color' => 'bg-gray-100 text-gray-600',
                                        'label' => 'Unknown',
                                    ];
                                @endphp

                                <span class="px-2 py-1 text-xs rounded-full {{ $status['color'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 w-[50px] flex justify-left items-center">
                                <button 
                                    type="button" 
                                    title="Edit location {{ $location->name }}" 
                                    data-modal-target="edit-modal"
                                    data-modal-toggle="edit-modal" 
                                    data-id="{{ $location->id }}"
                                    data-name="{{ $location->name }}" 
                                    data-code="{{ $location->code }}"
                                    data-description="{{ $location->description }}" 
                                    data-status="{{ $location->status }}"
                                    onclick="openEditModal(this)"
                                    class="group flex items-center space-x-1 text-gray-500 hover:text-blue-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path
                                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                        <path fill-rule="evenodd"
                                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                    </svg>
                                    {{-- <span class="hidden group-hover:inline transition-opacity duration-200"></span> --}}
                                </button>

                                {{-- <button 
                                            type="button"
                                            title="Void Authorization No. {{ $muzing->code }}"
                                            onclick="voidAuthorization({{ $muzing->code }})"
                                            class="ml-1 group flex items-center space-x-1 text-gray-500 hover:text-red-600 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                    <path d="M5.884 6.68a.5.5 0 1 0-.768.64L7.349 10l-2.233 2.68a.5.5 0 0 0 .768.64L8 10.781l2.116 2.54a.5.5 0 0 0 .768-.641L8.651 10l2.233-2.68a.5.5 0 0 0-.768-.64L8 9.219l-2.116-2.54z"/>
                                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                                </svg>                                            
                                        </button> --}}
                            </td>
                            {{-- <td class="px-4 py-3 text-right space-x-2 w-[150px]">
                            <a href="{{ route('locations.edit',$location->id) }}" class="text-gray-600 hover:text-gray-900">
                                ✏️
                            </a>
                            <form action="{{ route('location.destroy',$location->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700">🗑</button>
                            </form>
                        </td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                No locations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- Create location modal -->
    <div id="add-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-2 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                        Add New Location
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
                <form action="{{ route('location.store') }}" method="POST">
                    @csrf
                    <div class="grid gap-2 mb-4 sm:grid-cols-1">
                        <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-3">
                            <div class="w-full md:col-span-1">
                                <label for="code"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Code*</label>
                                <input type="text" name="code" id="code"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. HQ-MAIN" required>
                            </div>
                            <div class="w-full md:col-span-2">
                                <label for="name"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Name*</label>
                                <input type="text" name="name" id="name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Headquarters Main Office" required>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="description"
                                class="block text-xs font-medium text-gray-900 dark:text-white">Description</label>
                            <textarea type="text" name="description" id="description" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                placeholder="Location description"></textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="status"
                                class="block text-xs font-medium text-gray-900 dark:text-white">Status*</label>
                            <select id="status" name="status"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                required>
                                {{-- <option selected="">Select product type</option> --}}
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                                <option value="2">Under Maintenance</option>
                            </select>
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
                        Add Location
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- End create location modal -->

    <!-- Modal  Edit-->
    <div id="edit-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Update Location
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="edit-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form id="editForm" class="p-4 md:p-5" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="edit_id" id="edit_id">
                    
                    <div class="grid gap-2 mb-4 sm:grid-cols-1">
                        <div class="grid gap-4 sm:grid-cols-1 md:grid-cols-3">
                            <div class="w-full md:col-span-1">
                                <label for="edit_code"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Code*</label>
                                <input type="text" name="edit_code" id="edit_code"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. HQ-MAIN" required>
                            </div>
                            <div class="w-full md:col-span-2">
                                <label for="edit_name"
                                    class="block text-xs font-medium text-gray-900 dark:text-white">Name*</label>
                                <input type="text" name="edit_name" id="edit_name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                    placeholder="e.g. Headquarters Main Office" required>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="edit_description"
                                class="block text-xs font-medium text-gray-900 dark:text-white">Description</label>
                            <textarea type="text" name="edit_description" id="edit_description" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                placeholder="Location description"></textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="edit_status"
                                class="block text-xs font-medium text-gray-900 dark:text-white">Status*</label>
                            <select id="edit_status" name="edit_status"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                                required>
                                {{-- <option selected="">Select product type</option> --}}
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                                <option value="2">Under Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="mt-2 text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                        {{-- <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg> --}}
                        Update Location
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- End edit modal -->

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

        function openEditModal(button) {
        const id = button.getAttribute('data-id');
        document.getElementById('edit_id').value = button.getAttribute('data-id');
        document.getElementById('edit_code').value = button.getAttribute('data-code');
        document.getElementById('edit_name').value = button.getAttribute('data-name');
        document.getElementById('edit_description').value = button.getAttribute('data-description');
        document.getElementById('edit_status').value = button.getAttribute('data-status');
        
        const form = document.getElementById('editForm');
        form.action = `/location/${id}`;
        }    
    </script>
@endsection
