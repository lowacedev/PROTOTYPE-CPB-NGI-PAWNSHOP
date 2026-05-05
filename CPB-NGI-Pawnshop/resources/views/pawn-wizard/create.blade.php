<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('New Pawn Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg relative">
                
                <!-- Stepper Progress -->
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <div class="flex items-center justify-between relative">
                        <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 dark:bg-gray-700 z-0"></div>
                        <div id="progress-bar" class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-blue-600 z-0 transition-all duration-300" style="width: 0%;"></div>
                        
                        <!-- Step Indicators -->
                        @foreach(['Customer Info', 'Item Details', 'Loan Computation', 'Review & Submit'] as $index => $label)
                        <div class="step-indicator relative z-10 flex flex-col items-center" data-step="{{ $index + 1 }}">
                            <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-white dark:border-gray-800 flex items-center justify-center font-bold text-sm text-gray-600 dark:text-gray-300 transition-colors duration-300 indicator-circle">
                                {{ $index + 1 }}
                            </div>
                            <span class="mt-2 text-xs font-semibold text-gray-500 dark:text-gray-400 indicator-text hidden sm:block">{{ $label }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-6">
                    <form id="pawn-wizard-form" method="POST" action="{{ route('pawn.wizard.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Display Validation Errors -->
                        @if ($errors->any())
                            <div class="mb-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-600 dark:text-red-200 px-4 py-3 rounded relative">
                                <strong class="font-bold">Oops! Please check the form below for errors.</strong>
                                <ul class="mt-2 list-disc list-inside text-sm">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-600 dark:text-red-200 px-4 py-3 rounded relative">
                                <strong class="font-bold">Error!</strong>
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="mb-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-600 dark:text-green-200 px-4 py-3 rounded relative">
                                <strong class="font-bold">Success!</strong>
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif

                        <!-- ================= STEP 1: CUSTOMER KYC ================= -->
                        <div class="wizard-step" data-step="1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Step 1: Customer (KYC)</h3>
                            
                            <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Is this an existing or new customer?</label>
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="customer_type" value="existing" class="text-blue-600" checked onchange="toggleCustomerMode()">
                                        <span class="ml-2 dark:text-gray-200">Existing Customer</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="customer_type" value="new" class="text-blue-600" onchange="toggleCustomerMode()" {{ old('customer_type') == 'new' ? 'checked' : '' }}>
                                        <span class="ml-2 dark:text-gray-200">New Customer</span>
                                    </label>
                                </div>
                            </div>

                            <!-- EXISTING CUSTOMER SEARCH -->
                            <div id="existing-customer-section" class="space-y-4">
                                <div>
                                    <x-input-label for="customer_search" :value="__('Search Customer (Name or Phone)')" />
                                    <div class="relative">
                                        <x-text-input id="customer_search" class="block mt-1 w-full" type="text" placeholder="Type to search..." autocomplete="off" />
                                        <div id="search-results" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                                    </div>
                                </div>
                                <div id="selected-customer-card" class="hidden p-4 border border-green-300 bg-green-50 dark:bg-green-900/30 dark:border-green-800 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-green-800 dark:text-green-300" id="sel-cust-name"></h4>
                                            <p class="text-sm text-green-700 dark:text-green-400" id="sel-cust-phone"></p>
                                            <p class="text-sm text-green-700 dark:text-green-400" id="sel-cust-address"></p>
                                        </div>
                                        <button type="button" onclick="clearCustomerSelection()" class="text-red-500 hover:text-red-700 text-sm font-semibold">Change</button>
                                    </div>
                                    <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">
                                </div>
                            </div>

                            <!-- NEW CUSTOMER FORM -->
                            <div id="new-customer-section" class="hidden space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="first_name" :value="__('First Name *')" />
                                        <x-text-input id="first_name" name="first_name" class="block mt-1 w-full" type="text" :value="old('first_name')" />
                                    </div>
                                    <div>
                                        <x-input-label for="middle_name" :value="__('Middle Name')" />
                                        <x-text-input id="middle_name" name="middle_name" class="block mt-1 w-full" type="text" :value="old('middle_name')" />
                                    </div>
                                    <div>
                                        <x-input-label for="last_name" :value="__('Last Name *')" />
                                        <x-text-input id="last_name" name="last_name" class="block mt-1 w-full" type="text" :value="old('last_name')" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="phone_number" :value="__('Phone Number *')" />
                                        <x-text-input id="phone_number" name="phone_number" class="block mt-1 w-full" type="text" :value="old('phone_number')" />
                                    </div>
                                    <div>
                                        <x-input-label for="email" :value="__('Email (Optional)')" />
                                        <x-text-input id="email" name="email" class="block mt-1 w-full" type="email" :value="old('email')" />
                                    </div>
                                </div>

                                <h4 class="font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-2">Address</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="region_id" :value="__('Region *')" />
                                        <select id="region_id" name="region_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md">
                                            <option value="">Select Region</option>
                                            @foreach($regions as $region)
                                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="province_id" :value="__('Province *')" />
                                        <select id="province_id" name="province_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md" disabled>
                                            <option value="">Select Province</option>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="city_id" :value="__('City *')" />
                                        <select id="city_id" name="city_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md" disabled>
                                            <option value="">Select City</option>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="barangay_id" :value="__('Barangay *')" />
                                        <select id="barangay_id" name="barangay_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md" disabled>
                                            <option value="">Select Barangay</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="address_line" :value="__('Detailed Address')" />
                                    <x-text-input id="address_line" name="address_line" class="block mt-1 w-full" type="text" :value="old('address_line')" />
                                </div>

                                <h4 class="font-semibold text-gray-700 dark:text-gray-300 border-b dark:border-gray-700 pb-2">ID Verification</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="id_type" :value="__('ID Type *')" />
                                        <select id="id_type" name="id_type" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md">
                                            <option value="">Select ID Type</option>
                                            <option value="national_id">National ID</option>
                                            <option value="passport">Passport</option>
                                            <option value="driver_license">Driver's License</option>
                                            <option value="sss">SSS</option>
                                            <option value="philhealth">PhilHealth</option>
                                            <option value="voters_id">Voter's ID</option>
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label for="id_number" :value="__('ID Number *')" />
                                        <x-text-input id="id_number" name="id_number" class="block mt-1 w-full" type="text" :value="old('id_number')" />
                                    </div>
                                    <div class="md:col-span-2">
                                        <x-input-label for="id_image" :value="__('Upload ID Image *')" />
                                        <input id="id_image" name="id_image" type="file" accept="image/*" class="block mt-1 w-full text-sm dark:text-gray-300">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ================= STEP 2: ITEM DETAILS ================= -->
                        <div class="wizard-step hidden" data-step="2">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Step 2: Item Details</h3>
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="item_name" :value="__('Item Name *')" />
                                    <x-text-input id="item_name" name="item_name" class="block mt-1 w-full" type="text" :value="old('item_name')" />
                                </div>
                                <div>
                                    <x-input-label for="category_id" :value="__('Category *')" />
                                    <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="assessed_value" :value="__('Assessed Value (₱) *')" />
                                        <x-text-input id="assessed_value" name="assessed_value" class="block mt-1 w-full bg-yellow-50 dark:bg-yellow-900/20" type="number" step="0.01" :value="old('assessed_value')" oninput="computeLoan()" />
                                        <p class="text-xs text-gray-500 mt-1">This value will be used for loan computation.</p>
                                    </div>
                                    <div>
                                        <x-input-label for="condition" :value="__('Condition *')" />
                                        <select id="condition" name="condition" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md">
                                            <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                            <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                                            <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                            <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="item_description" :value="__('Description / Serial Numbers / Defects')" />
                                    <textarea id="item_description" name="item_description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md">{{ old('item_description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- ================= STEP 3: LOAN DETAILS ================= -->
                        <div class="wizard-step hidden" data-step="3">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Step 3: Loan Computation</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Inputs -->
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="loan_percentage" :value="__('Loan Percentage (%) *')" />
                                        <x-text-input id="loan_percentage" name="loan_percentage" class="block mt-1 w-full" type="number" step="1" value="{{ old('loan_percentage', 60) }}" oninput="computeLoan()" />
                                        <p class="text-xs text-gray-500 mt-1">Usually 50% to 70% of Assessed Value.</p>
                                    </div>
                                    <div>
                                        <x-input-label for="interest_rate" :value="__('Interest Rate (% per month) *')" />
                                        <x-text-input id="interest_rate" name="interest_rate" class="block mt-1 w-full" type="number" step="0.01" value="{{ old('interest_rate', 5) }}" oninput="computeLoan()" />
                                    </div>
                                    <div>
                                        <x-input-label for="term_days" :value="__('Term (Days) *')" />
                                        <x-text-input id="term_days" name="term_days" class="block mt-1 w-full" type="number" step="1" value="{{ old('term_days', 30) }}" oninput="computeLoan()" />
                                    </div>
                                </div>

                                <!-- Auto-Computed Display -->
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                    <h4 class="font-bold text-blue-900 dark:text-blue-300 border-b border-blue-200 dark:border-blue-800 pb-2 mb-4">Computed Values</h4>
                                    
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-blue-800 dark:text-blue-400">Assessed Value:</span>
                                            <span class="font-semibold text-blue-900 dark:text-blue-300" id="display_assessed">₱0.00</span>
                                        </div>
                                        <div class="flex justify-between items-center text-lg">
                                            <span class="font-bold text-blue-900 dark:text-blue-300">Principal Loan:</span>
                                            <span class="font-bold text-2xl text-blue-900 dark:text-blue-300" id="display_principal">₱0.00</span>
                                            <input type="hidden" name="loan_amount" id="loan_amount" value="{{ old('loan_amount') }}">
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-blue-800 dark:text-blue-400">Total Interest:</span>
                                            <span class="font-semibold text-blue-900 dark:text-blue-300" id="display_interest">₱0.00</span>
                                        </div>
                                        <div class="flex justify-between border-t border-blue-200 dark:border-blue-800 pt-2">
                                            <span class="font-bold text-blue-900 dark:text-blue-300">Maturity Date:</span>
                                            <span class="font-bold text-blue-900 dark:text-blue-300" id="display_maturity">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ================= STEP 4: REVIEW & SUBMIT ================= -->
                        <div class="wizard-step hidden" data-step="4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Step 4: Review Transaction</h3>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-6 space-y-6">
                                <!-- Review summary filled via JS -->
                                <div>
                                    <h4 class="font-bold text-gray-700 dark:text-gray-300 uppercase text-sm border-b dark:border-gray-600 pb-1 mb-2">Customer</h4>
                                    <p class="text-gray-900 dark:text-white" id="rev_customer"></p>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-700 dark:text-gray-300 uppercase text-sm border-b dark:border-gray-600 pb-1 mb-2">Item</h4>
                                    <p class="text-gray-900 dark:text-white" id="rev_item"></p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-bold text-gray-700 dark:text-gray-300 uppercase text-sm border-b dark:border-gray-600 pb-1 mb-2">Loan Amount</h4>
                                        <p class="text-xl font-bold text-green-600 dark:text-green-400" id="rev_loan"></p>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-700 dark:text-gray-300 uppercase text-sm border-b dark:border-gray-600 pb-1 mb-2">Maturity Date</h4>
                                        <p class="text-xl font-bold text-red-600 dark:text-red-400" id="rev_maturity"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg text-sm text-yellow-800 dark:text-yellow-400">
                                <strong>Warning:</strong> Ensure all details are correct.
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="mt-8 pt-5 border-t border-gray-200 dark:border-gray-700 flex justify-between">
                            <button type="button" id="btn-prev" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition hidden font-semibold">Back</button>
                            <div class="ml-auto">
                                <button type="button" id="btn-next" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">Next Step</button>
                                <button type="submit" id="btn-submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition hidden font-semibold">Confirm Transaction</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- WIZARD & JS LOGIC -->
    <script>
        // -- Stepper Logic --
        let currentStep = 1;
        const totalSteps = 4;
        
        function updateUI() {
            // Hide all steps, show current
            document.querySelectorAll('.wizard-step').forEach(el => {
                el.classList.add('hidden');
                if (parseInt(el.getAttribute('data-step')) === currentStep) {
                    el.classList.remove('hidden');
                }
            });

            // Update Progress Bar
            const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
            document.getElementById('progress-bar').style.width = `${progress}%`;

            // Update Step Indicators
            document.querySelectorAll('.step-indicator').forEach(el => {
                const step = parseInt(el.getAttribute('data-step'));
                const circle = el.querySelector('.indicator-circle');
                if (step < currentStep) {
                    circle.className = 'w-8 h-8 rounded-full bg-blue-600 border-2 border-white dark:border-gray-800 flex items-center justify-center font-bold text-sm text-white transition-colors duration-300 indicator-circle';
                    circle.innerHTML = '✓';
                } else if (step === currentStep) {
                    circle.className = 'w-8 h-8 rounded-full bg-blue-600 border-2 border-white dark:border-gray-800 flex items-center justify-center font-bold text-sm text-white transition-colors duration-300 indicator-circle ring-4 ring-blue-200 dark:ring-blue-900';
                    circle.innerHTML = step;
                } else {
                    circle.className = 'w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-white dark:border-gray-800 flex items-center justify-center font-bold text-sm text-gray-600 dark:text-gray-300 transition-colors duration-300 indicator-circle';
                    circle.innerHTML = step;
                }
            });

            // Update Buttons
            document.getElementById('btn-prev').classList.toggle('hidden', currentStep === 1);
            if (currentStep === totalSteps) {
                document.getElementById('btn-next').classList.add('hidden');
                document.getElementById('btn-submit').classList.remove('hidden');
                prepareReview(); // Build review text
            } else {
                document.getElementById('btn-next').classList.remove('hidden');
                document.getElementById('btn-submit').classList.add('hidden');
            }
        }

        document.getElementById('btn-next').addEventListener('click', () => {
            // Simple front-end required field check could go here
            if (currentStep < totalSteps) {
                currentStep++;
                updateUI();
            }
        });

        document.getElementById('btn-prev').addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                updateUI();
            }
        });

        // -- Customer Logic --
        function toggleCustomerMode() {
            const type = document.querySelector('input[name="customer_type"]:checked').value;
            if (type === 'existing') {
                document.getElementById('existing-customer-section').classList.remove('hidden');
                document.getElementById('new-customer-section').classList.add('hidden');
            } else {
                document.getElementById('existing-customer-section').classList.add('hidden');
                document.getElementById('new-customer-section').classList.remove('hidden');
            }
        }

        // AJAX Customer Search
        const searchInput = document.getElementById('customer_search');
        const searchResults = document.getElementById('search-results');
        
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value;
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }
            searchTimeout = setTimeout(() => {
                fetch(`/api/customers/search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.length > 0) {
                            data.forEach(cust => {
                                const div = document.createElement('div');
                                div.className = 'p-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b dark:border-gray-700 last:border-0';
                                div.innerHTML = `<p class="font-bold dark:text-white">${cust.name}</p><p class="text-sm text-gray-500 dark:text-gray-400">${cust.phone}</p>`;
                                div.addEventListener('click', () => selectCustomer(cust));
                                searchResults.appendChild(div);
                            });
                            searchResults.classList.remove('hidden');
                        } else {
                            searchResults.innerHTML = '<div class="p-3 text-gray-500">No customers found.</div>';
                            searchResults.classList.remove('hidden');
                        }
                    });
            }, 300);
        });

        // Hide search on click outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        function selectCustomer(cust) {
            document.getElementById('customer_id').value = cust.id;
            document.getElementById('sel-cust-name').textContent = cust.name;
            document.getElementById('sel-cust-phone').textContent = cust.phone;
            document.getElementById('sel-cust-address').textContent = cust.address || 'No address';
            
            searchInput.value = '';
            searchResults.classList.add('hidden');
            searchInput.parentElement.classList.add('hidden');
            document.getElementById('selected-customer-card').classList.remove('hidden');
        }

        function clearCustomerSelection() {
            document.getElementById('customer_id').value = '';
            document.getElementById('selected-customer-card').classList.add('hidden');
            searchInput.parentElement.classList.remove('hidden');
        }

        // -- Computation Logic --
        function computeLoan() {
            const assessed = parseFloat(document.getElementById('assessed_value').value) || 0;
            const pct = parseFloat(document.getElementById('loan_percentage').value) || 0;
            const rate = parseFloat(document.getElementById('interest_rate').value) || 0;
            const days = parseInt(document.getElementById('term_days').value) || 0;

            const loanAmt = assessed * (pct / 100);
            const interest = loanAmt * (rate / 100);
            
            // Calculate maturity date
            const maturityDate = new Date();
            maturityDate.setDate(maturityDate.getDate() + days);
            const dateStr = maturityDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

            document.getElementById('display_assessed').textContent = `₱${assessed.toFixed(2)}`;
            document.getElementById('display_principal').textContent = `₱${loanAmt.toFixed(2)}`;
            document.getElementById('loan_amount').value = loanAmt.toFixed(2);
            document.getElementById('display_interest').textContent = `₱${interest.toFixed(2)}`;
            document.getElementById('display_maturity').textContent = dateStr;
        }

        // -- Review Logic --
        function prepareReview() {
            // Customer
            const type = document.querySelector('input[name="customer_type"]:checked').value;
            let custText = '';
            if (type === 'existing') {
                custText = document.getElementById('sel-cust-name').textContent || 'Not Selected!';
            } else {
                custText = `${document.getElementById('first_name').value} ${document.getElementById('last_name').value} (New Customer)`;
            }
            document.getElementById('rev_customer').textContent = custText;

            // Item
            const itemName = document.getElementById('item_name').value || 'Unnamed Item';
            const itemCat = document.getElementById('category_id').options[document.getElementById('category_id').selectedIndex]?.text || '';
            document.getElementById('rev_item').textContent = `${itemName} - ${itemCat}`;

            // Loan
            document.getElementById('rev_loan').textContent = document.getElementById('display_principal').textContent;
            document.getElementById('rev_maturity').textContent = document.getElementById('display_maturity').textContent;
        }

        // Initialization
        document.addEventListener('DOMContentLoaded', () => {
            toggleCustomerMode();
            computeLoan();
            
            // PSGC Cascading Dropdowns for New Customer
            const r=document.getElementById('region_id'), p=document.getElementById('province_id'), c=document.getElementById('city_id'), b=document.getElementById('barangay_id');
            function reset(s,ph){s.innerHTML=`<option value="">${ph}</option>`;s.disabled=true;}
            function fill(s,data,ph){s.innerHTML=`<option value="">${ph}</option>`;data.forEach(i=>{const o=document.createElement('option');o.value=i.id;o.textContent=i.name;s.appendChild(o);});s.disabled=false;}
            if(r){
                r.addEventListener('change',function(){reset(p,'Select Province');reset(c,'Select City');reset(b,'Select Barangay');if(this.value)fetch(`/api/provinces/${this.value}`).then(r=>r.json()).then(d=>fill(p,d,'Select Province'));});
                p.addEventListener('change',function(){reset(c,'Select City');reset(b,'Select Barangay');if(this.value)fetch(`/api/cities/${this.value}`).then(r=>r.json()).then(d=>fill(c,d,'Select City'));});
                c.addEventListener('change',function(){reset(b,'Select Barangay');if(this.value)fetch(`/api/barangays/${this.value}`).then(r=>r.json()).then(d=>fill(b,d,'Select Barangay'));});
            }
        });
    </script>
</x-app-layout>
