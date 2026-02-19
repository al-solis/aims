@extends('dashboard')
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    @php
        use Carbon\Carbon;
    @endphp
    <div class="max-w-3xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 pt-1">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 pt-1"
                data-success="true">
                {{ session('success') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Clear form fields after successful submission
                    clearModalFields();
                });
            </script>
        @endif

        <div class="bg-white rounded-xl shadow-xs p-3 sm:p-8">
            <div class="text-center mb-4">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">
                    Supply Receiving
                </h2>
                <p class="text-sm text-gray-600">
                    Receive items to update inventory.
                </p>
            </div>
            <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">

            <form action="" method="POST" id="receivingForm">
                @csrf
                @method('PUT')
                <div class="grid ml-1 mr-1 mt-2 gap-2 mb-2 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <label for="description"
                            class="block text-xs font-medium text-gray-900 dark:text-white">Description*</label>
                        <input type="text" name="description" id="description"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="Type receiving desciption." required></input>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="received_date" class="block text-xs font-medium text-gray-900 dark:text-white">Received
                            Date*</label>
                        <input type="date" name="received_date" id="received_date" max="{{ now()->format('Y-m-d') }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"></input>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="reference_no" class="block text-xs font-medium text-gray-900 dark:text-white">Reference
                            No*</label>
                        <input type="text" name="reference_no" id="reference_no"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="Type reference/ PO number." required></input>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="supplier_id"
                            class="select2 block text-xs font-medium text-gray-900 dark:text-white">Supplier*</label>
                        <select id="supplier_id" name="supplier_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required>
                            <option selected>Select supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="employee_id"
                            class="select2 block text-xs font-medium text-gray-900 dark:text-white">Received By*</label>
                        <select id="employee_id" name="employee_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required>
                            <option selected>Select employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->last_name }}, {{ $employee->first_name }}
                                    {{ $employee->middle_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="remarks"
                            class="block text-xs font-medium text-gray-900 dark:text-white">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="Provide receiving details, etc."></textarea>
                    </div>

                    <div class="sm:col-span-1">
                        <label for="supplies_id"
                            class="select2 block text-xs font-medium text-gray-900 dark:text-white">Product*</label>
                        <select id="supplies_id" name="supplies_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required>
                            <option selected>Select item</option>
                            @foreach ($supplies as $supply)
                                <option value="{{ $supply->id }}">{{ $supply->code }} ({{ $supply->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <button id="add-item-btn" type="button"
                            class="mt-2 h-fit text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-md text-xs px-4 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                            Add Item
                        </button>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mt-4 mb-2">Item/s Received</h3>
                <div class="bg-white border rounded-xl overflow-x-auto overflow-y-auto md:overflow-visible scroll-smooth">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-200 text-gray-600">
                            <tr class="rounded-xl">
                                <th class="px-4 py-3 text-left w-[120px]">Item</th>
                                <th class="px-4 py-3 text-left w-[120px]">Qty</th>
                                <th class="px-4 py-3 text-left w-[200px]">UOM</th>
                                <th class="px-4 py-3 text-left w-[100px]">Price</th>
                                <th class="px-4 py-3 text-left w-[100px]">Total</th>
                                <th class="px-4 py-3 text-left w-[140px]">Action</th>
                            </tr>
                        </thead>

                        <tbody id="receivingBody" class="divide-y">
                            {{-- Dynamic rows will be added here via JavaScript --}}
                        </tbody>
                    </table>
                    <!-- Pagination Links -->
                    {{-- <div
                        class="w-full md:w-auto text-xs flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0 mb-2">
                        {{ $receivings->links() }}
                    </div> --}}
                </div>

                <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">
                <div class="mt-5 flex justify-end gap-x-2">
                    <a href="{{ route('asset.index') }}" type="button" id="closeButton"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </a>

                    <button type="submit" id="saveButton"
                        class="py-1.5 sm:py-2 px-3 inline-flex items-center gap-x-2 border text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-floppy" viewBox="0 0 16 16">
                            <path d="M11 2H9v3h2z" />
                            <path
                                d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z" />
                        </svg>
                        Save Transaction
                    </button>
                </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#supplies_id').select2({
                placeholder: "Select item",
                allowClear: true,
                width: '100%'
            });

            $('#employee_id').select2({
                placeholder: "Select employee",
                allowClear: true,
                width: '100%'
            });

            $('#supplier_id').select2({
                placeholder: "Select supplier",
                allowClear: true,
                width: '100%'
            });
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 
                ${type === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 
                type === 'error' ? 'bg-red-100 text-red-700 border border-red-300' : 
                'bg-blue-100 text-blue-700 border border-blue-300'}`;
            toast.textContent = message;
            toast.style.animation = 'slideInRight 0.3s ease-out';

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        function formatDateForInput(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);
            if (isNaN(date)) return '';

            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.querySelector('[data-success="true"]');
            if (successMessage) {
                setTimeout(() => {
                    successMessage.remove();
                }, 3000); // Remove after 3 seconds
            }

        });
    </script>

@endsection
