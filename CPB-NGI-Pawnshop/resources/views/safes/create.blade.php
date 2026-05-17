<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Safe') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('safes.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Is Personal Checkbox -->
                        <div class="flex items-center">
                            <input type="checkbox" id="is_personal" name="is_personal" value="1" {{ old('is_personal') ? 'checked' : '' }} class="rounded dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800">
                            <label for="is_personal" class="ms-2 text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('This safe is for a specific person') }}</label>
                        </div>

                        <!-- Customer Selection (shown when is_personal is checked) -->
                        <div id="customer-selection" class="hidden">
                            <x-input-label for="customer_id" :value="__('Select Customer')" />
                            <select id="customer_id" name="customer_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                <option value="">{{ __('Choose a customer...') }}</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->email }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div>
                            <x-input-label for="location" :value="__('Location')" />
                            <select id="location" name="location" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" required>
                                <option value="">{{ __('Choose a location...') }}</option>
                                @foreach(config('safes.locations') as $key => $label)
                                    <option value="{{ $key }}" {{ old('location') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" rows="4">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Items Capacity -->
                        <div>
                            <x-input-label for="items_capacity" :value="__('Items Capacity (Maximum number of items)')" />
                            <x-text-input id="items_capacity" class="block mt-1 w-full" type="number" name="items_capacity" :value="old('items_capacity', 10)" min="1" required />
                            <x-input-error :messages="$errors->get('items_capacity')" class="mt-2" />
                        </div>

                        <!-- Monetary Capacity -->
                        <div>
                            <x-input-label for="capacity" :value="__('Monetary Capacity (Maximum total value of items)')" />
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('Example: 50000 means this safe can hold items worth up to ₱50,000') }}</p>
                            <x-text-input id="capacity" class="block mt-1 w-full" type="number" step="0.01" name="capacity" :value="old('capacity', config('safes.default_capacity'))" required />
                            <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                        </div>



                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" name="notes" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" rows="4">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex gap-4">
                            <x-primary-button>{{ __('Create Safe') }}</x-primary-button>
                            <a href="{{ route('safes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isPersonalCheckbox = document.getElementById('is_personal');
            const customerSelection = document.getElementById('customer-selection');

            // Toggle customer selection visibility
            function toggleCustomerSelection() {
                if (isPersonalCheckbox.checked) {
                    customerSelection.classList.remove('hidden');
                } else {
                    customerSelection.classList.add('hidden');
                }
            }

            // Initial check
            toggleCustomerSelection();

            // Listen for changes
            isPersonalCheckbox.addEventListener('change', toggleCustomerSelection);
        });
    </script>
</x-app-layout>
