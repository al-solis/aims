@extends('dashboard')
@section('content')
    @php use Carbon\Carbon; @endphp

    {{-- Colors come from <html data-theme="..."> defined in the layout; do not set data-theme here --}}
    <div class="ft-page max-w-5xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">

        <div class="ft-card">
            <div class="ft-card-head">
                <h1>Clearance Approval Routing</h1>
                <p>Set which departments sign off on a clearance, and in what order.</p>
            </div>

            <div class="ft-body">
                <div class="grid gap-8 lg:grid-cols-5 items-start">

                    {{-- Left: form --}}
                    <div id="formCard" class="lg:col-span-2 lg:sticky lg:top-6">
                        <h3 id="formTitle" class="ft-section-title" style="margin-top: 0;">Add approval step</h3>
                        <p id="formHint" class="text-xs text-gray-500 mb-4">
                            New departments are added to the end of the sequence. Use the arrows to reorder.
                        </p>

                        <div id="formError" class="ft-alert ft-alert-error hidden text-xs"></div>

                        <div>
                            <label for="location_id" class="ft-label">
                                Department/Location <span class="text-red-600">*</span>
                            </label>
                            <select id="location_id" class="ft-field">
                                <option value="">Select a department</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-5 flex gap-2">
                            <button id="add-routing" type="button" class="ft-btn ft-btn-primary flex-1 justify-center">
                                Add step
                            </button>
                            <button id="cancel-edit" type="button" class="ft-btn ft-btn-ghost" style="display: none;">
                                Cancel
                            </button>
                        </div>
                    </div>

                    {{-- Right: approval sequence --}}
                    <div class="lg:col-span-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="ft-section-title" style="margin: 0;">Approval sequence</h3>
                            <span id="stepCount" class="text-xs text-gray-500"></span>
                        </div>

                        <ol id="routingList"></ol>

                        <div id="emptyState" class="ft-empty hidden">
                            <p class="font-semibold" style="color: var(--ft-ink);">No approval steps yet</p>
                            <p class="text-xs mt-1">Add the first department using the form.</p>
                        </div>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="ft-divider mt-8 pt-5 flex justify-end gap-x-2">
                    <a href="{{ route('setup.index') }}" class="ft-btn ft-btn-ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Setup
                    </a>
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
            fetch(`/setup/clearance-routing/list`)
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
                            'move-btn p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 disabled:opacity-30 disabled:cursor-not-allowed disabled:hover:bg-transparent';

                        // Colors come from the theme variables so the list follows data-theme
                        const cardStyle = isEditing ?
                            'background: var(--ft-mist); border-color: var(--ft-accent); box-shadow: 0 0 0 2px var(--ft-accent);' :
                            'background: var(--ft-mist); border-color: var(--ft-line);';

                        listEl.insertAdjacentHTML('beforeend', `
                            <li class="relative flex gap-3 ${isLast ? '' : 'pb-4'}">
                                ${isLast ? '' : '<span class="absolute left-4 top-8 bottom-0 w-px" style="background: var(--ft-border);" aria-hidden="true"></span>'}
                                <span class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                    style="background: var(--ft-ink); color: var(--ft-on-ink);">
                                    ${i + 1}
                                </span>
                                <div class="flex flex-1 items-center justify-between rounded-lg border px-3 py-2" style="${cardStyle}">
                                    <span class="text-sm font-semibold" style="color: var(--ft-ink);">${name}</span>
                                    <div class="flex items-center gap-0.5">
                                        <button type="button" title="Move ${name} up" aria-label="Move ${name} up"
                                            class="${moveBtn}" data-id="${r.id}" data-direction="up" ${isFirst ? 'disabled' : ''}>
                                            ${chevronUp}
                                        </button>
                                        <button type="button" title="Move ${name} down" aria-label="Move ${name} down"
                                            class="${moveBtn}" data-id="${r.id}" data-direction="down" ${isLast ? 'disabled' : ''}>
                                            ${chevronDown}
                                        </button>
                                        <span class="w-px h-5 mx-1" style="background: var(--ft-border);" aria-hidden="true"></span>
                                        <button type="button" title="Edit ${name}" aria-label="Edit ${name}"
                                            class="edit-btn p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300"
                                            data-id="${r.id}"
                                            data-location="${r.location_id}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                            </svg>
                                        </button>
                                        <button type="button" title="Delete ${name}" aria-label="Delete ${name}"
                                            class="delete-btn p-1.5 rounded text-gray-500 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-300"
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

            const url = editingId ? `/setup/clearance-routing/${editingId}` : '/setup/clearance-routing';
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
                fetch(`/setup/clearance-routing/${moveBtn.dataset.id}/move`, {
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
                cancelBtn.style.display = '';

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

                fetch(`/setup/clearance-routing/${deleteBtn.dataset.id}`, {
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
            cancelBtn.style.display = 'none';
        }
    </script>
@endsection
