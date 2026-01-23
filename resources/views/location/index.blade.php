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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v16m8-8H4"/>
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
                        </svg>'
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
                        </svg>'
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
                        </svg>'
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($cards as $card)
                <div class="bg-white border rounded-xl p-4 flex items-center gap-4">
                    
                    <div class="w-10 h-10 rounded-lg bg-{{ $card['color'] }}-100 
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

        {{-- Filters --}}
        <div class="flex flex-col md:flex-row gap-4 text-xs md:text-sm">
            <div class="md:w-2/3 w-full">
                <input type="text"
                    placeholder="Search by name, code, or city..."
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
            </div>

            <div class="md:w-1/3 w-full">
                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                    {{-- <option selected="">Select product type</option>--}}
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
                        <th scope="col" class="px-4 py-3 text-center w-[150px]">Actions</th>
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
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $location->status === 'Active'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-600' }}">
                                {{ $location->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2 w-[150px]">
                            <a href="{{ route('locations.edit',$location->id) }}" class="text-gray-600 hover:text-gray-900">
                                ✏️
                            </a>
                            <form action="{{ route('location.destroy',$location->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700">🗑</button>
                            </form>
                        </td>
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
    <div id="add-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full">
        <div class="relative p-4 w-full max-w-sm h-full md:h-auto">
            <!-- Modal content -->
            <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
                <!-- Modal header -->
                <div class="flex justify-between items-center pb-4 mb-2 rounded-t border-b sm:mb-5 dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                        Add New Location
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="add-modal">
                        <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                    <!-- Modal body -->
                    <form action="" method="POST">
                        @csrf              
                        <div class="grid gap-2 mb-4 sm:grid-cols-1">
                            <div class="col-span-2">
                                <label for="name" class="block text-xs font-medium text-gray-900 dark:text-white">Name</label>
                                <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500" placeholder="e.g. Main Office" required=>
                            </div>                            
                            
                            <div class="sm:col-span-2">
                                <label for="description" class="block text-xs font-medium text-gray-900 dark:text-white">Description</label>
                                <textarea type="text" name="description" id="description" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500" placeholder="Location description" required></textarea>
                            </div> 
                                                                              
                            <div class="sm:col-span-2">
                                <label for="status" class="block text-xs font-medium text-gray-900 dark:text-white">Status</label>
                                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500">
                                    {{-- <option selected="">Select product type</option>--}}
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                    <option value="2">Under Maintenance</option>
                                </select>
                            </div>
                         
                        </div>
                        <button type="submit" class="text-white inline-flex items-center bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-md text-xs px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            <svg class="mr-1 -ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                            Add Location
                        </button>
                    </form>
                </div>
            </div>
    </div>
    <!-- End create location -->

@endsection
