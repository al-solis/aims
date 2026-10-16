@extends('dashboard')
@section('content')
    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Auth;

        $saveDisabled = in_array(Auth::user()->role, [0, 2]) || Auth::user()->id != $clearanceHeader->created_by;

        $statuses = [
            0 => 'Pending',
            1 => 'Returned',
            2 => 'Damaged',
            3 => 'Lost',
        ];
    @endphp

    <div class="ft-page max-w-4xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">

        @if ($errors->any())
            <div class="ft-alert ft-alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif (session('success'))
            <div class="ft-alert ft-alert-success" data-success="true">
                {{ session('success') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Clear form fields after successful submission
                    clearModalFields();
                });
            </script>
        @endif

        <div class="ft-card">
            <div class="ft-card-head">
                <h1>Clearance Request</h1>
                <p>Display employee clearance details.</p>
            </div>

            <div class="ft-body">

                {{-- Employee --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="ft-info">
                        <p class="ft-eyebrow">Employee Name</p>
                        <p class="ft-info-value">
                            {{ $clearanceHeader->employee ? $clearanceHeader->employee->last_name . ', ' . $clearanceHeader->employee->first_name . ' ' . $clearanceHeader->employee->middle_name : 'N/A' }}
                        </p>
                    </div>
                    <div class="ft-info">
                        <p class="ft-eyebrow">Department</p>
                        <p class="ft-info-value">
                            {{ $clearanceHeader->employee && $clearanceHeader->employee->location ? $clearanceHeader->employee->location->description : 'N/A' }}
                        </p>
                    </div>
                </div>

                <form action="{{ route('clearance.update-details', $clearanceHeader->id) }}" method="POST"
                    id="clearanceForm">
                    @csrf
                    @method('PUT')

                    {{-- Request details --}}
                    <h3 class="ft-section-title">Request Details</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <label for="type" class="ft-label">Request Type*</label>
                            <select name="type" id="type" class="ft-field" required>
                                <option value="" disabled selected>Select request type</option>
                                <option value="1" {{ old('type', $clearanceHeader->type) == 1 ? 'selected' : '' }}>
                                    Transfer</option>
                                <option value="2" {{ old('type', $clearanceHeader->type) == 2 ? 'selected' : '' }}>
                                    Resignation</option>
                                <option value="3" {{ old('type', $clearanceHeader->type) == 3 ? 'selected' : '' }}>
                                    Contract Ending</option>
                                <option value="4" {{ old('type', $clearanceHeader->type) == 4 ? 'selected' : '' }}>
                                    Termination</option>
                            </select>
                        </div>

                        <div class="sm:col-span-1">
                            <label for="expected_date" class="ft-label">Expected Date*</label>
                            <input type="date" name="expected_date" id="expected_date" class="ft-field"
                                value="{{ old('expected_date', $clearanceHeader->expected_date ? Carbon::parse($clearanceHeader->expected_date)->format('Y-m-d') : '') }}"
                                required>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="remarks" class="ft-label">Remarks</label>
                            <textarea name="remarks" id="remarks" rows="3" class="ft-field" placeholder="Enter remarks">{{ old('remarks', $clearanceHeader->remarks) }}</textarea>
                        </div>
                    </div>

                    {{-- Approval history --}}
                    <h3 class="ft-section-title">Approval History</h3>
                    <div class="ft-table-wrap">
                        <table class="ft-table">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Approver</th>
                                    <th>Action</th>
                                    <th>Date</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($approvalHistory as $approval)
                                    <tr>
                                        <td>{{ $approval->location ? $approval->location->name : 'N/A' }}</td>
                                        <td>
                                            {{ $approval->approver ? $approval->approver->lname . ', ' . $approval->approver->fname . ' ' . $approval->approver->mname : 'N/A' }}
                                        </td>
                                        <td>
                                            @if ($approval->approved == 1)
                                                <span class="ft-badge ft-badge-ok">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M13.78 4.22a.75.75 0 0 0-1.06 0L6.5 10.44l-2.72-2.72a.75.75 0 1 0-1.06 1.06l3.25 3.25a.75.75 0 0 0 1.06 0l7.25-7.25a.75.75 0 0 0 0-1.06z" />
                                                    </svg>
                                                    Approved
                                                </span>
                                            @elseif ($approval->approved == 0)
                                                <span class="ft-badge ft-badge-no">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 16 16">
                                                        <path
                                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                                    </svg>
                                                    Rejected
                                                </span>
                                            @else
                                                <span class="ft-badge ft-badge-na">{{ $approval->action }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $approval->created_at ? Carbon::parse($approval->created_at)->format('M d, Y h:i A') : 'N/A' }}
                                        </td>
                                        <td>{{ $approval->remarks ?? '-' }}</td>
                                    </tr>
                                @endforeach
                                @if ($approvalHistory->isEmpty())
                                    <tr>
                                        <td colspan="5" class="ft-empty">No approval history found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Clearance details --}}
                    @if ($clearanceHeader->status != 0)
                        <h3 class="ft-section-title">Clearance Details</h3>
                        <div class="ft-table-wrap">
                            <table class="ft-table">
                                <thead>
                                    <tr>
                                        <th>Asset</th>
                                        <th style="width: 90px;">Qty</th>
                                        <th>Purchase</th>
                                        <th style="width: 140px;">Actual</th>
                                        <th>Total</th>
                                        <th style="width: 140px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clearanceHeader->clearance_details as $detail)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                                {{ $detail->asset->name ?? 'N/A' }}
                                            </td>

                                            <td>
                                                <input type="number" min="1" step="1" name="qty[]"
                                                    class="qty ft-field" value="{{ $detail->quantity }}" readonly>
                                            </td>

                                            <td>{{ number_format($detail->purchase_cost, 2) }}</td>

                                            <td>
                                                <input type="number" step="0.01" name="actual[]" class="actual ft-field"
                                                    value="{{ $detail->actual_cost ?? $detail->purchase_cost }}">
                                            </td>

                                            <!-- TOTAL -->
                                            <td>
                                                <input type="hidden" name="total[]" class="total"
                                                    value="{{ $detail->total }}">
                                                <input type="text" class="total-display ft-field ft-field-plain"
                                                    value="{{ number_format($detail->total, 2) }}" readonly>
                                            </td>

                                            <!-- ACTION / STATUS -->
                                            <td>
                                                <select name="status[]" class="ft-status"
                                                    data-status="{{ $detail->status }}">
                                                    @foreach ($statuses as $value => $label)
                                                        <option value="{{ $value }}" @selected($detail->status == $value)>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($clearanceHeader->clearance_details->isEmpty())
                                        <tr>
                                            <td colspan="6" class="ft-empty">No asset accountability found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="ft-divider mt-8 pt-5 flex justify-end gap-x-2">
                        <a href="{{ route('clearance.index') }}" id="closeButton" class="ft-btn ft-btn-ghost">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Back
                        </a>

                        {{-- Show when pending --}}
                        @if ($clearanceHeader->status == 0)
                            <button type="submit" id="saveBtn" class="ft-btn ft-btn-primary"
                                @if ($saveDisabled) disabled @endif>
                                Save Changes
                            </button>
                        @elseif ($clearanceHeader->current_approver == $userLocation->location_id && $clearanceHeader->status == 1)
                            <button type="submit" id="saveBtn" class="ft-btn ft-btn-primary">
                                Save Changes
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('actual')) {
                const row = e.target.closest('tr');
                const qty = parseFloat(row.querySelector('.qty').value) || 0;
                const actual = parseFloat(e.target.value) || 0;
                const total = qty * actual;

                row.querySelector('.total').value = total.toFixed(2);

                // Formatted display
                row.querySelector('.total-display').value =
                    total.toLocaleString('en-US', {
                        minimumFractionDigits: 2
                    });
            }
        });

        // Color the status select by its current value
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('ft-status')) {
                e.target.dataset.status = e.target.value;
            }
        });
    </script>
@endsection
