@extends('dashboard')
@section('content')
    <div class="max-w-2xl px-4 py-10 sm:px-6 lg:px-8 lg:py-6 mx-auto">
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
                    Employee Management
                </h2>
                <p class="text-sm text-gray-600">
                    Manage your employee.
                </p>
            </div>
            <hr style="border: 0; height: 1px; background-color: #ccc; margin: 10px 0;">

            <form method="POST" action="{{ route('employee.store') }}" enctype="multipart/form-data" id="employeeForm">
                @csrf

                <div class="grid gap-2 sm:grid-cols-2 sm:gap-2 mb-2">
                    <input type="hidden" id="id" name="id" value="0">

                    <input type="hidden" name="employee_path" id="employee_path"
                        value="{{ old('photo', $employee->photo_path ?? '') }}">

                    <div class="sm:col-span-1">
                        <label for="imgPreview" class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
                            Employee Photo (Click to upload)
                        </label>
                        <div class="flex items-center gap-3 mb-2">
                            <img id="empPreview"
                                class="inline-block size-32 rounded-lg ring-white dark:ring-neutral-900 cursor-pointer hover:opacity-80 transition-opacity duration-200 border border-gray-300"
                                src="{{ asset('storage/default/employee.jpg') }}" alt="Employee photo"
                                title="Click to upload photo">
                            <input type="file" id="photoInput" name="photo" accept="image/*" class="hidden">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Click on the image to upload a photo (Max: 2MB)</p>
                    </div>

                    <div class="w-full">
                        <label for="idno" class="block text-xs font-medium text-gray-900 dark:text-white">ID
                            No*</label>
                        <input type="text" name="sku" id="sku"
                            class="mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="e.g. 001-26, 2026-00001" required>
                        <small id="id-feedback" class="text-red-500 text-sm mt-1 hidden">
                            ID number already exists in another record.
                        </small>

                        <label for="date" class="block text-xs font-medium text-gray-900 dark:text-white">Date
                            Hired*</label>
                        <input type="date" name="date" id="date"
                            class="mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="e.g. 04/08/1984" required>

                        <label for="status"
                            class="block text-xs font-medium text-gray-900 dark:text-white">Status*</label>
                        <select id="status" name="status"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            required>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                            <option value="2">On leave</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-2 sm:grid-cols-1 md:grid-cols-3">
                    <div class="w-full">
                        <label for="lname" class="block text-xs font-medium text-gray-900 dark:text-white">Last
                            Name*</label>
                        <input type="text" name="lname" id="lname"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="e.g. Dela Cruz" required="">
                    </div>
                    <div class="w-full">
                        <label for="fname" class="block text-xs font-medium text-gray-900 dark:text-white">First
                            Name*</label>
                        <input type="text" name="fname" id="fname"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="e.g. Juan" required="">
                    </div>
                    <div class="w-full">
                        <label for="mname" class="block text-xs font-medium text-gray-900 dark:text-white">Middle
                            Name</label>
                        <input type="text" name="mname" id="mname"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"
                            placeholder="e.g. Santos">
                    </div>
                </div>

                <div class="mt-5 flex justify-end gap-x-2">
                    <a href="{{ route('employee.index') }}" type="button" id="closeButton"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray border border-gray-300 bg-gray-100 rounded-lg hover:bg-gray-200 ">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back
                    </a>
                    <button type="submit" id="saveBtn"
                        class="py-1.5 sm:py-2 px-3 inline-flex items-center gap-x-2 border text-xs font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function clearModalFields() {
            // Clear all form fields
            const form = document.querySelector('form');
            form.reset();

            // Reset image to default
            const imgPreview = document.getElementById('empPreview');
            imgPreview.src = "{{ asset('storage/default/employee.jpg') }}";

            // Clear hidden path input
            document.getElementById('employee_path').value = '';

            // Remove any success messages after a delay
            setTimeout(() => {
                const successMessage = document.querySelector('[data-success]');
                if (successMessage) {
                    successMessage.remove();
                }
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const imgPreview = document.getElementById('empPreview');
            const fileInput = document.getElementById('photoInput');
            const imgPathInput = document.getElementById('employee_path');

            // Make image clickable to trigger file input
            imgPreview.addEventListener('click', function() {
                fileInput.click();
            });

            // Handle file selection
            fileInput.addEventListener('change', function() {
                if (fileInput.files && fileInput.files[0]) {
                    // Validate file size (2MB max)
                    const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                    if (fileInput.files[0].size > maxSize) {
                        alert('File size exceeds 2MB limit. Please choose a smaller file.');
                        fileInput.value = '';
                        return;
                    }

                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                    if (!validTypes.includes(fileInput.files[0].type)) {
                        alert('Please select a valid image file (JPEG, PNG, JPG, GIF, WEBP).');
                        fileInput.value = '';
                        return;
                    }

                    // Show preview immediately
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imgPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(fileInput.files[0]);

                    // Upload to server
                    uploadImageToServer(fileInput.files[0]);
                }
            });

            // Function to upload image to server
            function uploadImageToServer(file) {
                const formData = new FormData();
                formData.append('photo', file);
                formData.append('_token', '{{ csrf_token() }}');

                imgPreview.classList.add('opacity-50');
                imgPreview.title = 'Uploading...';

                fetch('{{ route('employee.uploadImage') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        imgPreview.classList.remove('opacity-50');

                        if (data.path) {
                            imgPathInput.value = data.path;
                            imgPreview.src = '{{ asset('storage') }}/' + data.path;
                            imgPreview.title = 'Photo uploaded successfully!';


                            const successIndicator = document.createElement('div');
                            successIndicator.className = 'text-green-500 text-xs mt-1';
                            successIndicator.textContent = '✓ Photo uploaded successfully';
                            successIndicator.id = 'upload-success';

                            const existingIndicator = document.getElementById('upload-success');
                            if (existingIndicator) {
                                existingIndicator.remove();
                            }

                            imgPreview.parentNode.appendChild(successIndicator);

                            setTimeout(() => {
                                const indicator = document.getElementById('upload-success');
                                if (indicator) {
                                    indicator.remove();
                                }
                                imgPreview.title = 'Click to change photo';
                            }, 3000);

                        } else if (data.error) {
                            throw new Error(data.error);
                        }
                    })
                    .catch(err => {
                        console.error('Upload error:', err);
                        imgPreview.classList.remove('opacity-50');

                        imgPreview.src = "{{ asset('storage/default/employee.jpg') }}";
                        imgPreview.title = 'Upload failed. Click to try again.';
                        fileInput.value = '';
                        imgPathInput.value = '';

                        alert('Error uploading image: ' + err.message);
                    });
            }
        });
    </script>
@endsection
