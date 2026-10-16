@extends('dashboard')

@section('title', $targetRole->exists ? 'Edit Role' : 'New Role')

@section('content')
    {{-- Colors come from <html data-theme="..."> defined in the layout; do not set data-theme here --}}
    <div class="ft-page max-w-xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">

        @if ($errors->any())
            <div class="ft-alert ft-alert-error">
                <div class="font-semibold text-sm">Please correct the following errors:</div>
                <ul class="mt-1 list-disc list-inside text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $isEdit = $targetRole->exists;
            $formAction = $isEdit ? route('setup.roles.update', $targetRole->id) : route('setup.roles.store');
        @endphp

        <form method="POST" action="{{ $formAction }}">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="ft-card">
                <div class="ft-card-head">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1>{{ $isEdit ? 'Edit Role' : 'New Role' }}</h1>
                            <p>{{ $isEdit ? 'Update this role' : 'Define a new role' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('setup.role.index') }}" class="ft-btn ft-btn-ghost"
                                style="color: var(--ft-on-ink); background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                                Back
                            </a>
                            <button type="submit" class="ft-btn ft-btn-primary"
                                style="color: var(--ft-ink); background: var(--ft-surface); border-color: var(--ft-surface);">
                                {{ $isEdit ? 'Update Role' : 'Save Role' }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="ft-body space-y-4">
                    <div>
                        <label for="name" class="ft-label">
                            Role Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $targetRole->name) }}"
                            placeholder="e.g. Administrator" required class="ft-field">
                    </div>

                    <div>
                        <label for="description" class="ft-label">Description</label>
                        <textarea id="description" name="description" rows="3" class="ft-field"
                            placeholder="e.g. Administrator role with full access.">{{ old('description', $targetRole->description) }}</textarea>
                    </div>

                    <div>
                        <label for="is_active" class="ft-label">Status</label>
                        <select id="is_active" name="is_active" class="ft-field">
                            <option value="1" {{ old('is_active', $targetRole->is_active) === '1' ? 'selected' : '' }}>
                                Active</option>
                            <option value="0" {{ old('is_active', $targetRole->is_active) === '0' ? 'selected' : '' }}>
                                Inactive</option>
                        </select>
                    </div>

                    {{-- @if ($isEdit)
                        <div class="ft-info">
                            <p class="text-xs" style="color: var(--ft-muted);">
                                To configure what this role can see and do, use
                                <a href="{{ route('admin.access', ['role_id' => $targetRole->id]) }}"
                                    style="color: var(--ft-accent); text-decoration: underline;">Access Rights</a>
                                after saving.
                            </p>
                        </div>
                    @endif --}}
                </div>
            </div>
        </form>
    </div>
@endsection
