<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Item') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('items.update', $item) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Item Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $item->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $item->category_id) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="safe_id" :value="__('Safe (Optional)')" />
                            <select id="safe_id" name="safe_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                <option value="">{{ __('No Safe') }}</option>
                                @foreach($safes as $safe)
                                    <option value="{{ $safe->id }}" @selected(old('safe_id', $item->safe_id) == $safe->id)>{{ $safe->safe_code }} - {{ $safe->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('safe_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="appraised_value" :value="__('Appraised Value')" />
                            <x-text-input id="appraised_value" class="block mt-1 w-full" type="number" name="appraised_value" :value="old('appraised_value', $item->appraised_value)" step="0.01" required />
                            <x-input-error :messages="$errors->get('appraised_value')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="condition" :value="__('Condition')" />
                            <select id="condition" name="condition" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                <option value="">{{ __('Select Condition') }}</option>
                                <option value="excellent" @selected(old('condition', $item->condition) === 'excellent')>{{ __('Excellent') }}</option>
                                <option value="good" @selected(old('condition', $item->condition) === 'good')>{{ __('Good') }}</option>
                                <option value="fair" @selected(old('condition', $item->condition) === 'fair')>{{ __('Fair') }}</option>
                                <option value="poor" @selected(old('condition', $item->condition) === 'poor')>{{ __('Poor') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">{{ old('description', $item->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="location" :value="__('Storage Location (Optional)')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $item->location)" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">{{ old('notes', $item->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="is_available" :value="__('Status')" />
                            <div class="mt-2 flex items-center">
                                <input type="checkbox" id="is_available" name="is_available" value="1" @checked(old('is_available', $item->is_available)) class="rounded dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800">
                                <label for="is_available" class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Available') }}</label>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <x-primary-button>{{ __('Update Item') }}</x-primary-button>
                            <a href="{{ route('items.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
