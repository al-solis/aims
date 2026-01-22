@extends('dashboard')
@section('content')
    <div class="max-w-1xl px-4 py-2 sm:px-6 lg:px-8 lg:py-6 mx-auto">
        {{-- <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Location Management</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">Manage your organization's locations and sub-locations for asset tracking.</p> --}}
        @extends('layouts.app')

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

            <a href="{{ route('locations.create') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v16m8-8H4"/>
                </svg>
                Add Location
            </a>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $cards = [
                    ['title'=>'Total Locations','value'=>$totalLocations,'color'=>'blue'],
                    ['title'=>'Active Locations','value'=>$activeLocations,'color'=>'green'],
                    ['title'=>'Total Sub-Locations','value'=>$totalSubLocations,'color'=>'purple'],
                    ['title'=>'Total Capacity','value'=>$totalCapacity,'color'=>'orange'],
                ];
            @endphp

            @foreach($cards as $card)
            <div class="bg-white border rounded-xl p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-{{ $card['color'] }}-100 flex items-center justify-center">
                    <span class="w-4 h-4 bg-{{ $card['color'] }}-500 rounded-full"></span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ $card['title'] }}</p>
                    <p class="text-xl font-semibold">{{ $card['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text"
                    placeholder="Search by name, code, or city..."
                    class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-gray-200">
            </div>

            <select class="px-4 py-2 border rounded-lg">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        {{-- Table --}}
        <div class="bg-white border rounded-xl overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left">Code</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Address</th>
                        <th class="px-4 py-3 text-left">Contact</th>
                        <th class="px-4 py-3 text-left">Sub-Locations</th>
                        <th class="px-4 py-3 text-left">Capacity</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($locations as $location)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $location->code }}</td>
                        <td class="px-4 py-3">{{ $location->name }}</td>
                        <td class="px-4 py-3">{{ $location->type }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $location->address }}<br>
                            {{ $location->city }}, {{ $location->state }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $location->contact_name }}<br>
                            <span class="text-gray-500">{{ $location->contact_phone }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="#" class="text-blue-600 hover:underline">
                                {{ $location->sub_locations_count }} sub-locations
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $location->capacity }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $location->status === 'Active'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-600' }}">
                                {{ $location->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('locations.edit',$location->id) }}" class="text-gray-600 hover:text-gray-900">
                                ✏️
                            </a>
                            <form action="{{ route('locations.destroy',$location->id) }}" method="POST" class="inline">
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
