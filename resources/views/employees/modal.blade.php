<!-- Modal Background -->
<div id="employeeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <!-- Modal Content -->
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 id="modal_title" class="text-xl font-semibold">Add New Employee</h3>
            <button class="text-gray-500 hover:text-gray-800 text-2xl close-modal">&times;</button>
        </div>

        <!-- Modal Body -->
        <form id="employeeForm">
            @csrf
            {{-- Input tersembunyi untuk method (POST/PUT) dan ID --}}
            <input type="hidden" name="_method" id="form_method" value="POST">
            <input type="hidden" name="employee_id" id="employee_id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- User Details -->
                <div class="col-span-1 md:col-span-2 font-bold text-lg mb-2 border-b">User Account</div>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    <span id="name_error" class="text-red-500 text-xs error-message"></span>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    <span id="email_error" class="text-red-500 text-xs error-message"></span>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative mt-1">
                        <input type="password" name="password" id="password" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 pr-10">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                            <span class="toggle-password cursor-pointer text-gray-400 hover:text-gray-600">
                                <svg class="icon-show h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg class="icon-hide h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.127 2.455.364m-6.91 6.91a3 3 0 014.243-4.243m-4.243 4.243l4.243-4.243m-1.245 6.354A6.975 6.975 0 0112 15a6.975 6.975 0 01-2.43-13.35l-1.022-1.022m-1.022 1.022L3 3m18 18l-3-3m-3-3l-3-3"></path></svg>
                            </span>
                        </div>
                    </div>
                    <span id="password_error" class="text-red-500 text-xs error-message"></span>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <div class="relative mt-1">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 pr-10">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                             <span class="toggle-password cursor-pointer text-gray-400 hover:text-gray-600">
                                <svg class="icon-show h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg class="icon-hide h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.127 2.455.364m-6.91 6.91a3 3 0 014.243-4.243m-4.243 4.243l4.243-4.243m-1.245 6.354A6.975 6.975 0 0112 15a6.975 6.975 0 01-2.43-13.35l-1.022-1.022m-1.022 1.022L3 3m18 18l-3-3m-3-3l-3-3"></path></svg>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Employee Details -->
                <div class="col-span-1 md:col-span-2 font-bold text-lg mt-4 mb-2 border-b">Employee Details</div>
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                    <input type="text" name="nik" id="nik" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    <span id="nik_error" class="text-red-500 text-xs error-message"></span>
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <span id="phone_error" class="text-red-500 text-xs error-message"></span>
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" id="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    <span id="address_error" class="text-red-500 text-xs error-message"></span>
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role (System Access)</label>
                    <select name="role" id="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="" disabled selected>Select a role</option>
                        @isset($roles)
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ Str::ucfirst($role->name) }}</option>
                            @endforeach
                        @endisset
                    </select>
                    <span id="role_error" class="text-red-500 text-xs error-message"></span>
                </div>
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700">Position / Jabatan</label>
                    <input type="text" name="position" id="position" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Manager, Staff">
                    <span id="position_error" class="text-red-500 text-xs error-message"></span>
                </div>
                <div>
                    <label for="hire_date" class="block text-sm font-medium text-gray-700">Hire Date</label>
                    <input type="date" name="hire_date" id="hire_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <span id="hire_date_error" class="text-red-500 text-xs error-message"></span>
                </div>
                <div>
                    <label for="salary_monthly" class="block text-sm font-medium text-gray-700">Salary (Monthly)</label>
                    <input type="text" name="salary_monthly" id="salary_monthly" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Rp 0">
                    <span id="salary_monthly_error" class="text-red-500 text-xs error-message"></span>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end items-center border-t pt-4 mt-4">
                <button type="button" id="cancelBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </button>
                <button type="submit" id="submitBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Save Employee
                </button>
            </div>
        </form>
    </div>
</div>

