@extends('dashboard')

@section('title', 'Access Rights')

@section('content')
    {{-- Colors come from <html data-theme="..."> defined in the layout; do not set data-theme here --}}
    <div class="ft-page max-w-6xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto" x-data="accessRightsMatrix()">

        @if (session('success'))
            <div id="success-alert" class="ft-alert ft-alert-success">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ session('success') }}
                    </div>
                    <button type="button" onclick="this.closest('[id$=-alert]').style.display='none'"
                        class="hover:opacity-75">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="ft-card">
            <div class="ft-card-head">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1>Access Rights</h1>
                        <p>Define what each role can see and do, per module and sub-module.</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('setup.role.index') }}" class="ft-btn ft-btn-ghost"
                            style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>
                    </div>
                </div>
            </div>

            {{-- Role selector --}}
            <form method="GET" action="{{ route('setup.access.index') }}"
                class="flex flex-wrap items-center gap-3 border-b px-6 py-4" style="border-color: var(--ft-line);">
                <label class="ft-label" style="margin-bottom: 0;">Role</label>
                <select name="role_id" onchange="this.form.submit()" class="ft-field min-w-[220px]">
                    @forelse ($roles as $role)
                        <option value="{{ $role->id }}" @selected($selectedRoleId == $role->id)>{{ $role->name }}</option>
                    @empty
                        <option value="">No roles yet</option>
                    @endforelse
                </select>
                <a href="{{ route('setup.roles.create') }}" class="text-xs hover:underline"
                    style="color: var(--ft-accent);">+ New Role</a>
            </form>

            @if ($roles->isEmpty())
                <div class="ft-body">
                    <div class="ft-info text-center" style="border-left: 0; border: 1px dashed var(--ft-border);">
                        <p class="text-sm" style="color: var(--ft-muted);">
                            No roles exist yet.
                            <a href="{{ route('setup.roles.create') }}" class="hover:underline"
                                style="color: var(--ft-accent);">Create one first</a>.
                        </p>
                    </div>
                </div>
            @else
                <form method="POST" action="{{ route('setup.access.update') }}" id="access-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="role_id" value="{{ $selectedRoleId }}">

                    <div class="flex flex-col gap-3 border-b px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
                        style="border-color: var(--ft-line);">
                        <p class="text-xs flex-1" style="color: var(--ft-muted);">
                            Toggling a column header checks/unchecks that permission for every row currently shown.
                        </p>
                        <div class="flex gap-2 flex-shrink-0">
                            <a href="{{ route('setup.role.index') }}" class="ft-btn ft-btn-ghost">Back</a>
                            <button type="submit" class="ft-btn ft-btn-primary">Save Changes</button>
                        </div>
                    </div>

                    <div class="ft-table-wrap" style="border: 0; border-radius: 0;">
                        <table class="ft-table">
                            <thead>
                                <tr>
                                    <th class="text-left">Module / Sub-Module</th>
                                    <th class="text-center w-20">
                                        <div class="flex flex-col items-center gap-1">
                                            <span>Create</span>
                                            <input type="checkbox"
                                                @change="toggleColumn('can_create', $event.target.checked)">
                                        </div>
                                    </th>
                                    <th class="text-center w-20">
                                        <div class="flex flex-col items-center gap-1">
                                            <span>Read</span>
                                            <input type="checkbox"
                                                @change="toggleColumn('can_read', $event.target.checked)">
                                        </div>
                                    </th>
                                    <th class="text-center w-20">
                                        <div class="flex flex-col items-center gap-1">
                                            <span>Update</span>
                                            <input type="checkbox"
                                                @change="toggleColumn('can_update', $event.target.checked)">
                                        </div>
                                    </th>
                                    <th class="text-center w-20">
                                        <div class="flex flex-col items-center gap-1">
                                            <span>Delete</span>
                                            <input type="checkbox"
                                                @change="toggleColumn('can_delete', $event.target.checked)">
                                        </div>
                                    </th>
                                    <th class="text-center w-16">All</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($modules as $module)
                                    @php
                                        $subModules = $module->subModules;
                                        $rowKeys = $subModules->isEmpty()
                                            ? [$module->id . '-null']
                                            : $subModules->map(fn($sm) => $module->id . '-' . $sm->id)->all();

                                        // Define modules that should be disabled
                                        $disabledModules = [
                                            'AR',
                                            'FA',
                                            'IA',
                                            'PUR',
                                            'SALES',
                                            'BUDGET',
                                            'COST',
                                            'MASTER',
                                        ];
                                        $isDisabled = in_array($module->code, $disabledModules);
                                    @endphp

                                    <!-- Module header row -->
                                    <tr style="background: var(--ft-mist);">
                                        <td colspan="5" class="px-6 py-2 font-semibold" style="color: var(--ft-ink);">
                                            {{ $module->name }}
                                            <span class="ml-2 font-normal" style="color: var(--ft-muted);">
                                                ({{ $module->code }})
                                            </span>
                                            @if ($isDisabled)
                                                <span class="ft-badge ft-badge-na ml-2">Features Not Applicable</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox"
                                                @change="toggleModule('{{ implode(',', $rowKeys) }}', $event.target.checked)"
                                                {{ $isDisabled ? 'disabled' : '' }}>
                                        </td>
                                    </tr>

                                    @if ($subModules->isEmpty())
                                        @php
                                            $key = $module->id . '-null';
                                            $existing = $rightsMap->get($key);
                                        @endphp
                                        <tr data-row-key="{{ $key }}">
                                            <td class="px-6 py-2 pl-10" style="color: var(--ft-muted);">
                                                <span>(module-level access)</span>
                                            </td>
                                            @foreach (['can_create', 'can_read', 'can_update', 'can_delete'] as $flag)
                                                <td class="px-3 py-2 text-center">
                                                    <input type="hidden"
                                                        name="rights[{{ $module->id }}][null][{{ $flag }}]"
                                                        value="0">
                                                    <input type="checkbox"
                                                        name="rights[{{ $module->id }}][null][{{ $flag }}]"
                                                        value="1" data-flag="{{ $flag }}"
                                                        data-row="{{ $key }}" @checked($existing && $existing->$flag)
                                                        {{ $isDisabled ? 'disabled' : '' }}>
                                                </td>
                                            @endforeach
                                            <td class="px-3 py-2 text-center">
                                                <input type="checkbox" data-row-all="{{ $key }}"
                                                    @change="toggleRow('{{ $key }}', $event.target.checked)"
                                                    {{ $isDisabled ? 'disabled' : '' }}>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($subModules as $subModule)
                                            @php
                                                $key = $module->id . '-' . $subModule->id;
                                                $existing = $rightsMap->get($key);
                                            @endphp
                                            <tr data-row-key="{{ $key }}">
                                                <td class="px-6 py-2 pl-10" style="color: var(--ft-text);">
                                                    {{ $subModule->name }}</td>
                                                @foreach (['can_create', 'can_read', 'can_update', 'can_delete'] as $flag)
                                                    <td class="px-3 py-2 text-center">
                                                        <input type="hidden"
                                                            name="rights[{{ $module->id }}][{{ $subModule->id }}][{{ $flag }}]"
                                                            value="0">
                                                        <input type="checkbox"
                                                            name="rights[{{ $module->id }}][{{ $subModule->id }}][{{ $flag }}]"
                                                            value="1" data-flag="{{ $flag }}"
                                                            data-row="{{ $key }}" @checked($existing && $existing->$flag)
                                                            {{ $isDisabled ? 'disabled' : '' }}>
                                                    </td>
                                                @endforeach
                                                <td class="px-3 py-2 text-center">
                                                    <input type="checkbox" data-row-all="{{ $key }}"
                                                        @change="toggleRow('{{ $key }}', $event.target.checked)"
                                                        {{ $isDisabled ? 'disabled' : '' }}>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="ft-empty">No active modules found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="ft-divider flex items-center justify-end px-6 py-4">
                        <button type="submit" class="ft-btn ft-btn-primary">Save Changes</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <script>
        function accessRightsMatrix() {
            return {
                toggleColumn(flag, checked) {
                    document.querySelectorAll(`input[data-flag="${flag}"]:not([disabled])`).forEach(cb => {
                        cb.checked = checked;
                    });
                },

                toggleRow(rowKey, checked) {
                    document.querySelectorAll(`input[data-row="${rowKey}"]:not([disabled])`).forEach(cb => {
                        cb.checked = checked;
                    });
                    const rowAll = document.querySelector(`input[data-row-all="${rowKey}"]:not([disabled])`);
                    if (rowAll) rowAll.checked = checked;
                },

                toggleModule(rowKeysCsv, checked) {
                    const rowKeys = rowKeysCsv.split(',');
                    rowKeys.forEach(rowKey => {
                        document.querySelectorAll(`input[data-row="${rowKey}"]:not([disabled])`).forEach(cb => {
                            cb.checked = checked;
                        });
                        const rowAll = document.querySelector(`input[data-row-all="${rowKey}"]:not([disabled])`);
                        if (rowAll) rowAll.checked = checked;
                    });
                },
            };
        }
    </script>
@endsection
