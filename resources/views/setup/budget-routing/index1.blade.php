@extends('dashboard')
@section('content')
    @php use Carbon\Carbon; @endphp

    <div class="max-w-5xl px-4 py-8 sm:px-6 lg:px-8 mx-auto">

        {{-- Page header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between mb-6">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Budget Request Approval Routing</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Set which departments sign off on a budget request, and in what order.
                </p>
            </div>
            <a href="{{ route('setup.index') }}"
                class="inline-flex items-center gap-2 self-start sm:self-auto px-4 py-2 text-xs font-medium text-gray-700 border border-gray-300 bg-white rounded-lg hover:bg-gray-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Setup
            </a>
        </div>

        <div class="grid gap-6 lg:grid-cols-5 items-start">

            {{-- Left: form --}}
            <div class="lg:col-span-2 lg:sticky lg:top-6">
                <div id="formCard" class="bg-white rounded-xl shadow-xs border border-gray-200 p-5">
                    <h3 id="formTitle" class="text-base font-bold text-gray-800">Add approval step</h3>
                    <p id="formHint" class="text-xs text-gray-500 mt-1 mb-4">
                        New departments are added to the end of the sequence. Use the arrows to reorder.
                    </p>

                    <div id="formError"
                        class="hidden mb-3 text-xs text-red-700 bg-red-50 border border-red-200 rounded p-2"></div>

                    <div>
                        <label for="location_id" class="block text-xs font-medium text-gray-700 mb-1">
                            Department/Location <span class="text-red-600">*</span>
                        </label>
                        <select id="location_id"
                            class="w-full text-xs border border-gray-300 rounded p-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Select a department</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-5 flex gap-2">
                        <button id="add-routing"
                            class="flex-1 text-white bg-green-600 hover:bg-green-700 text-sm font-medium rounded p-2">
                            Add step
                        </button>
                        <button id="cancel-edit" type="button"
                            class="hidden text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 hover:bg-gray-200 rounded px-4 py-2">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right: approval sequence --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-800">Approval sequence</h3>
                        <span id="stepCount" class="text-xs text-gray-500"></span>
                    </div>

                    <ol id="routingList"></ol>

                    <div id="emptyState" class="hidden text-center py-10">
                        <p class="text-sm font-medium text-gray-700">No approval steps yet</p>
                        <p class="text-xs text-gray-500 mt-1">Add the first department using the form.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        const listEl = document.getElementById('routingList');
        const emptyEl = document.getElementById('emptyState');
        const countEl = document.getElementById('stepCount');
        const addBtn = document.getElementById('add-routing');
        const cancelBtn = document.getElementById('cancel-edit');
        const errorEl = document.getElementById('formError');
        const csrf = '{{ csrf_token() }}';
        let editingId = null;

        const jsonHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf
        };

        const chevronUp =
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>';
        const chevronDown =
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';

        document.addEventListener('DOMContentLoaded', loadRoutings);

        function esc(str) {
            const d = document.createElement('div');
            d.textContent = str ?? '';
            return d.innerHTML;
        }

        function showError(msg) {
            errorEl.textContent = msg;
            errorEl.classList.remove('hidden');
        }

        function clearError() {
            errorEl.classList.add('hidden');
            errorEl.textContent = '';
        }

        function loadRoutings() {
            fetch(`/setup/budget-routing/list`)
                .then(res => res.json())
                .then(res => {
                    const data = res.record || [];
                    listEl.innerHTML = '';

                    emptyEl.classList.toggle('hidden', data.length > 0);
                    countEl.textContent = data.length ? `${data.length} step${data.length > 1 ? 's' : ''}` : '';

                    data.forEach((r, i) => {
                        const isFirst = i === 0;
                        const isLast = i === data.length - 1;
                        const name = esc(r.location.name);
                        const isEditing = String(r.id) === String(editingId);
                        const moveBtn =
                            'move-btn p-1.5 rounded text-gray-600 hover:bg-gray-200 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-transparent';

                        listEl.insertAdjacentHTML('beforeend', `
                            <li class="relative flex gap-3 ${isLast ? '' : 'pb-4'}">
                                ${isLast ? '' : '<span class="absolute left-4 top-8 bottom-0 w-px bg-gray-300" aria-hidden="true"></span>'}
                                <span class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-600 text-white text-xs font-bold">
                                    ${i + 1}
                                </span>
                                <div class="flex flex-1 items-center justify-between rounded-lg border px-3 py-2 ${isEditing ? 'border-blue-400 bg-blue-50' : 'border-gray-200 bg-gray-50'}">
                                    <span class="text-sm font-medium text-gray-800">${name}</span>
                                    <div class="flex items-center gap-0.5">
                                        <button type="button" title="Move ${name} up" aria-label="Move ${name} up"
                                            class="${moveBtn}" data-id="${r.id}" data-direction="up" ${isFirst ? 'disabled' : ''}>
                                            ${chevronUp}
                                        </button>
                                        <button type="button" title="Move ${name} down" aria-label="Move ${name} down"
                                            class="${moveBtn}" data-id="${r.id}" data-direction="down" ${isLast ? 'disabled' : ''}>
                                            ${chevronDown}
                                        </button>
                                        <span class="w-px h-5 bg-gray-300 mx-1" aria-hidden="true"></span>
                                        <button type="button" title="Edit ${name}" aria-label="Edit ${name}"
                                            class="edit-btn p-1.5 rounded text-blue-600 hover:bg-blue-100"
                                            data-id="${r.id}"
                                            data-location="${r.location_id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                            </svg>
                                        </button>
                                        <button type="button" title="Delete ${name}" aria-label="Delete ${name}"
                                            class="delete-btn p-1.5 rounded text-red-600 hover:bg-red-100"
                                            data-id="${r.id}"
                                            data-location_name="${name}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        `);
                    });
                });
        }

        // ADD / UPDATE
        addBtn.addEventListener('click', function() {
            clearError();

            const location = document.getElementById('location_id').value;

            if (!location) {
                showError('Select a department.');
                return;
            }

            const url = editingId ? `/setup/budget-routing/${editingId}` : '/setup/budget-routing';
            const method = editingId ? 'PUT' : 'POST';

            fetch(url, {
                    method: method,
                    headers: jsonHeaders,
                    body: JSON.stringify({
                        location_id: location
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.error) {
                        showError(res.error);
                    } else if (res.errors) {
                        showError(Object.values(res.errors).flat().join(' '));
                    } else {
                        resetForm();
                        loadRoutings();
                    }
                })
                .catch(() => showError('Something went wrong. Try again.'));
        });

        cancelBtn.addEventListener('click', function() {
            resetForm();
            loadRoutings();
        });

        // MOVE / EDIT / DELETE (delegated)
        document.addEventListener('click', function(e) {
            const moveBtn = e.target.closest('.move-btn');
            const editBtn = e.target.closest('.edit-btn');
            const deleteBtn = e.target.closest('.delete-btn');

            if (moveBtn && !moveBtn.disabled) {
                moveBtn.disabled = true;
                fetch(`/setup/budget-routing/${moveBtn.dataset.id}/move`, {
                        method: 'POST',
                        headers: jsonHeaders,
                        body: JSON.stringify({
                            direction: moveBtn.dataset.direction
                        })
                    })
                    .then(res => res.json())
                    .then(() => loadRoutings())
                    .catch(() => loadRoutings());
            }

            if (editBtn) {
                clearError();
                editingId = editBtn.dataset.id;

                document.getElementById('location_id').value = editBtn.dataset.location;
                document.getElementById('formTitle').textContent = 'Edit approval step';
                document.getElementById('formHint').textContent =
                    'Change the department for this step. Its position stays the same.';
                addBtn.textContent = 'Save changes';
                cancelBtn.classList.remove('hidden');

                document.getElementById('formCard').scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
                loadRoutings(); // re-render to highlight the step being edited
            }

            if (deleteBtn) {
                if (!confirm('Delete this record ' + deleteBtn.dataset.location_name + '?')) {
                    return;
                }

                fetch(`/setup/budget-routing/${deleteBtn.dataset.id}`, {
                        method: 'DELETE',
                        headers: jsonHeaders
                    })
                    .then(res => res.json())
                    .then(() => {
                        if (String(deleteBtn.dataset.id) === String(editingId)) resetForm();
                        loadRoutings();
                    });
            }
        });

        function resetForm() {
            editingId = null;
            clearError();
            document.getElementById('location_id').value = '';
            document.getElementById('formTitle').textContent = 'Add approval step';
            document.getElementById('formHint').textContent =
                'New departments are added to the end of the sequence. Use the arrows to reorder.';
            addBtn.textContent = 'Add step';
            cancelBtn.classList.add('hidden');
        }
    </script>
@endsection
