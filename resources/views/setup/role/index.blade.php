@extends('dashboard')

@section('title', 'Roles')

@section('content')
    {{-- Colors come from <html data-theme="..."> defined in the layout; do not set data-theme here --}}
    <div class="ft-page max-w-5xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">

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
                        <h1>Roles</h1>
                        <p>Manage roles. Configure what each role can access under
                            <a href="{{ route('setup.access.index') }}"
                                style="color: var(--ft-accent); text-decoration: underline;">Access Rights</a>.
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('setup.index') }}" class="ft-btn ft-btn-ghost">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>
                        <a href="{{ $canCreate ? route('setup.roles.create') : '#' }}"
                            class="ft-btn ft-btn-primary {{ !$canCreate ? 'pointer-events-none opacity-50 cursor-not-allowed' : '' }}"
                            @if (!$canCreate) aria-disabled="true" tabindex="-1" @endif>
                            + New Role
                        </a>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('setup.role.index') }}"
                class="flex flex-wrap items-center gap-3 border-b px-6 py-4" style="border-color: var(--ft-line);">
                <input type="text" name="searchname" value="{{ request('searchname') }}"
                    placeholder="Search by role name" class="ft-field flex-1 min-w-[220px]">
                <button type="submit" class="ft-btn ft-btn-ghost">Filter</button>
                @if (request()->filled('searchname'))
                    <a href="{{ route('setup.role.index') }}" class="text-xs hover:underline"
                        style="color: var(--ft-muted);">Clear</a>
                @endif
            </form>

            <div class="ft-table-wrap" style="border: 0; border-radius: 0;">
                <table class="ft-table">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th class="text-right">Users</th>
                            <th class="text-right w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td class="font-semibold" style="color: var(--ft-ink);">{{ $role->name }}</td>
                                <td style="color: var(--ft-muted);">{{ $role->description ?? '—' }}</td>
                                <td class="text-right tabular-nums" style="color: var(--ft-text);">
                                    {{ $role->users_count }}</td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        {{-- <a href="{{ route('admin.access', ['role_id' => $role->id]) }}"
                                            title="Manage Access"
                                            class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2" />
                                            </svg>
                                        </a> --}}
                                        <a href="{{ $canUpdate ? route('setup.roles.edit', $role) : '#' }}"
                                            title="Edit Role"
                                            class="p-1.5 rounded text-gray-500 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors
                                            {{ !$canUpdate ? 'pointer-events-none opacity-50 cursor-not-allowed' : '' }}
                                            ">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293a.5.5 0 0 1 0 .707z" />
                                                <path
                                                    d="M13.752 4.396l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12z" />
                                                <path fill-rule="evenodd"
                                                    d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="ft-empty">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                @if (!$canCreate && !$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create or update role information.
                    </p>
                @elseif (!$canCreate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to create role information.
                    </p>
                @elseif (!$canUpdate)
                    <p class="mt-2 mr-auto rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        You don't have permission to update role information.
                    </p>
                @endif
            </div>

            <div class="flex items-center justify-between border-t px-6 py-4 text-xs"
                style="border-color: var(--ft-line); color: var(--ft-muted);">
                <div>Showing {{ $roles->firstItem() ?? 0 }}-{{ $roles->lastItem() ?? 0 }} of {{ $roles->total() }}</div>
                {{ $roles->links() }}
            </div>
        </div>
    </div>
@endsection
