@extends('dashboard')
@section('content')
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <!-- Left: Title & Description -->
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Sub-Location Management</h1>
                <p class="text-sm text-gray-500">Manage sub-locations for asset tracking</p>
            </div>

            <!-- Right: Buttons -->
            <div class="flex items-center gap-2 mt-0">
                <a href="{{ route('location.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>

                <button data-modal-target="add-modal" data-modal-toggle="add-modal"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Sub-Location
                </button>                
            </div>
        </div>
    
        @php
            $cards = [
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
                [
                    'title' => 'Active Sub-Locations',
                    'value' => $activeSubLocations,
                    'color' => 'green',
                    'icon' => '
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 13l4 4L19 7" />
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
                <input type="text" placeholder="Search by name, code, or description..."
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
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Location</th>
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Code</th>
                        <th scope="col" class="px-4 py-3 text-left w-[200px]">Name</th>
                        <th scope="col" class="px-4 py-3 text-left w-[350px]">Description</th>                        
                        <th scope="col" class="px-4 py-3 text-left w-[100px]">Status</th>
                        <th scope="col" class="px-4 py-3 text-center w-[50px]">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($sublocations as $sublocation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium w-[100px]">{{ $sublocation->location->code }}</td>
                            <td class="px-4 py-3 font-medium w-[100px]">{{ $sublocation->code }}</td>
                            <td class="px-4 py-3 w-[200px]">{{ $sublocation->name }}</td>
                            <td class="px-4 py-3 w-[350px]">{{ $sublocation->description }}</td>                            
                            <td class="px-4 py-3 w-[100px] text-xs">
                                @php
                                    $statuses = [
                                        0 => ['color' => 'bg-red-100 text-red-600', 'label' => 'Inactive'],
                                        1 => ['color' => 'bg-green-100 text-green-700', 'label' => 'Active'],
                                        2 => ['color' => 'bg-yellow-100 text-yellow-700', 'label' => 'Under Maintenance'],
                                    ];
                                    $status = $statuses[$sublocation->status] ?? [
                                        'color' => 'bg-gray-100 text-gray-600',
                                        'label' => 'Unknown',
                                    ];
                                @endphp

                                <span class="px-2 py-1 text-xs rounded-full {{ $status['color'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 w-[50px] flex justify-center">
                                <button 
                                    type="button" 
                                    title="Edit sub-location {{ $sublocation->name }}" 
                                    data-modal-target="edit-modal"
                                    data-modal-toggle="edit-modal" 
                                    data-id="{{ $sublocation->id }}"
                                    data-name="{{ $sublocation->name }}" 
                                    data-code="{{ $sublocation->code }}"
                                    data-description="{{ $sublocation->description }}" 
                                    data-status="{{ $sublocation->status }}"
                                    onclick="openEditModal(this)"
                                    class="group flex space-x-1 text-gray-500 hover:text-blue-600 transition-colors">
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
                                edit
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
                                No sub-locations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection