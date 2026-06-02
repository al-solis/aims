@extends('dashboard')
@section('content')
    @php use Carbon\Carbon; @endphp

    <div class="max-w-3xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">

        <div class="bg-white rounded-xl shadow-xs p-3 sm:p-8">
            <div class="text-center mb-4">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
                    Budget Request Approval Routing
                </h2>
                <p class="text-sm text-gray-600">
                    Setup budget request approval routing.
                </p>
            </div>

            <hr class="my-3">

            <div class="grid gap-2 mb-4 sm:grid-cols-6">
                <div class="sm:col-span-4">
                    <label class="text-xs font-medium">Department/Location*</label>
                    <select id="location_id" class="w-full text-xs border rounded p-2">
                        <option value="">Select</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-1">
                    <label class="text-xs font-medium">Order*</label>
                    <input type="number" id="order" min="1" step="1"
                        class="w-full text-xs border rounded p-2">
                </div>

                <div class="sm:col-span-1 flex items-end">
                    <button id="add-routing" class="w-full text-white bg-green-600 text-sm rounded p-2">
                        Add
                    </button>
                </div>
            </div>

            <h3 class="text-lg font-bold mb-2">Routing Order</h3>

            <table class="min-w-full text-xs border">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left">Location/ Department</th>
                        <th class="px-3 py-2 text-left">Order</th>
                        <th class="px-3 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody id="routingBody"></tbody>
            </table>

            <div class="mt-5 flex justify-end gap-x-2">
                <a href="{{ route('setup.index') }}" type="button" id="closeButton"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadRoutings();
        });

        function loadRoutings() {
            fetch(`/setup/budget-routing/list`)
                .then(res => res.json())
                .then(res => {
                    const data = res.record;
                    const body = document.getElementById('routingBody');
                    body.innerHTML = '';

                    data.forEach(r => {
                        body.innerHTML += `
                <tr>
                    <td class="px-3 py-2">${r.location.name}</td>
                    <td class="px-3 py-2">${r.order}</td>
                    <td class="px-3 py-2">
                        <button title="Edit ${r.location.name}"
                            class="edit-btn text-blue-600"
                            data-id="${r.id}"
                            data-location="${r.location_id}"
                            data-location_name="${r.location.name}"
                            data-order="${r.order}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                                fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                            </svg>
                        </button>

                        <button title="Delete ${r.location.name}"
                            class="delete-btn text-red-600 ml-2"                            
                            data-id="${r.id}"
                            data-location_name="${r.location.name}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
                    });
                });
        }

        // ADD / UPDATE
        document.getElementById('add-routing').addEventListener('click', function() {
            const location = document.getElementById('location_id').value;
            const order = document.getElementById('order').value;
            const id = this.dataset.id;

            if (!location || !order) {
                alert('Fill all fields');
                return;
            }

            let url = '/setup/budget-routing';
            let method = 'POST';

            if (id) {
                url = `/setup/budget-routing/${id}`;
                method = 'PUT';
            }

            fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        location_id: location,
                        order: order
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.error) {
                        alert(res.error);
                    } else {
                        resetForm();
                        loadRoutings();
                    }
                });
        });

        // EDIT
        document.addEventListener('click', function(e) {

            const editBtn = e.target.closest('.edit-btn');

            if (editBtn) {
                document.getElementById('location_id').value = editBtn.dataset.location;
                document.getElementById('order').value = editBtn.dataset.order;

                const btn = document.getElementById('add-routing');
                btn.textContent = 'Update';
                btn.dataset.id = editBtn.dataset.id;
            }
        });

        // DELETE
        document.addEventListener('click', function(e) {

            const deleteBtn = e.target.closest('.delete-btn');

            if (deleteBtn) {

                if (!confirm('Delete this record ' + deleteBtn.dataset.location_name + '?')) {
                    return;
                }

                fetch(`/setup/budget-routing/${deleteBtn.dataset.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(() => loadRoutings());
            }
        });

        function resetForm() {
            document.getElementById('location_id').value = '';
            document.getElementById('order').value = '';

            const btn = document.getElementById('add-routing');
            btn.textContent = 'Add';
            delete btn.dataset.id;
        }
    </script>
@endsection
