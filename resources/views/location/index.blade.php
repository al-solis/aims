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

            <a href="{{ route('location.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v16m8-8H4"/>
                </svg>
                Add Location
            </a>
        </div>

        {{-- Stats Cards --}}
        @php
            $cards = [
                [
                    'title' => 'Total Locations',
                    'value' => $totalLocations,
                    'color' => 'blue',
                    'icon' => '
                        <svg xmlns="http://www.w3.org/2000/svg" class ="text-blue-600" width="16" height="16" fill="currentColor" class="bi bi-building" viewBox="0 0 16 16">
                        <path d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
                        <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3z"/>
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
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
            </div>

            <div class="md:w-1/3 w-full">
                <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                    {{-- <option selected="">Select product type</option>--}}
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
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
                            <a href="#" class="text-blue-600 hover:underline">
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
@endsection
