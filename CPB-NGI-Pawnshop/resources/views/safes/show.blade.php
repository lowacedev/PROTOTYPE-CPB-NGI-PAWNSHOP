<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $safe->name }} ({{ $safe->safe_code }})
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('safes.edit', $safe) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded dark:bg-yellow-600 dark:hover:bg-yellow-800">
                    Edit
                </a>
                <a href="{{ route('safes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded dark:bg-gray-600 dark:hover:bg-gray-800">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Safe Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Safe Code</h3>
                            <p class="text-lg font-semibold mt-1">{{ $safe->safe_code }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Location</h3>
                            <p class="text-lg font-semibold mt-1">{{ $safe->location }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Items Capacity</h3>
                            <p class="text-lg font-semibold mt-1">{{ $safe->items->count() }}/{{ $safe->items_capacity }}</p>
                        </div>
                        @if($safe->capacity)
                            <div>
                                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Monetary Capacity</h3>
                                <p class="text-lg font-semibold mt-1">₱{{ number_format($safe->capacity, 2) }}</p>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Current Value</h3>
                            <p class="text-lg font-semibold mt-1">₱{{ number_format($safe->current_value, 2) }}</p>
                        </div>
                    </div>

                    @if($safe->description)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Description</h3>
                            <p class="text-gray-900 dark:text-gray-100 mt-2">{{ $safe->description }}</p>
                        </div>
                    @endif

                    @if($safe->notes)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">Notes</h3>
                            <p class="text-gray-900 dark:text-gray-100 mt-2">{{ $safe->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items in Safe -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Items in Safe ({{ $safe->items->count() }}/{{ $safe->items_capacity }})</h3>

                    @if($safe->items->count() > 0)
                        <div class="space-y-6">
                            @foreach($safe->items as $item)
                                @php
                                    $latestTxn = $item->latest_transaction;
                                @endphp
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Item Details -->
                                        <div>
                                            <h4 class="font-semibold text-lg mb-3 text-gray-900 dark:text-gray-100">{{ $item->name }}</h4>
                                            <div class="space-y-2 text-sm">
                                                <div>
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">Item Code:</span>
                                                    <span class="text-gray-900 dark:text-gray-100">{{ $item->item_code }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">Category:</span>
                                                    <span class="text-gray-900 dark:text-gray-100">{{ $item->category->name ?? 'N/A' }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">Appraised Value:</span>
                                                    <span class="text-gray-900 dark:text-gray-100">₱{{ number_format($item->appraised_value, 2) }}</span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">Condition:</span>
                                                    <span class="px-2 py-1 rounded text-xs font-semibold inline-block ml-2
                                                        @if($item->condition === 'excellent')
                                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                        @elseif($item->condition === 'good')
                                                            bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                        @elseif($item->condition === 'fair')
                                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                        @else
                                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                        @endif
                                                    ">
                                                        {{ ucfirst($item->condition) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="font-medium text-gray-600 dark:text-gray-400">Added Date:</span>
                                                    <span class="text-gray-900 dark:text-gray-100">{{ $item->created_at->format('M d, Y \a\t H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Customer & Status Information -->
                                        <div>
                                            @if($latestTxn)
                                                <h4 class="font-semibold text-lg mb-3 text-gray-900 dark:text-gray-100">Customer Information</h4>
                                                <div class="space-y-2 text-sm bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                                    <div>
                                                        <span class="font-medium text-gray-600 dark:text-gray-400">Name:</span>
                                                        <span class="text-gray-900 dark:text-gray-100">{{ $latestTxn->customer->full_name }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-gray-600 dark:text-gray-400">Email:</span>
                                                        <span class="text-gray-900 dark:text-gray-100">{{ $latestTxn->customer->email ?? 'N/A' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-gray-600 dark:text-gray-400">Phone:</span>
                                                        <span class="text-gray-900 dark:text-gray-100">{{ $latestTxn->customer->phone_number }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-gray-600 dark:text-gray-400">Address:</span>
                                                        <span class="text-gray-900 dark:text-gray-100">{{ $latestTxn->customer->full_address }}</span>
                                                    </div>
                                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                                        <span class="font-medium text-gray-600 dark:text-gray-400">Transaction Status:</span>
                                                        <span class="px-2 py-1 rounded text-xs font-semibold inline-block ml-2
                                                            @if($latestTxn->status === 'active')
                                                                bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                            @elseif($latestTxn->status === 'redeemed')
                                                                bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                            @elseif($latestTxn->status === 'renewed')
                                                                bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                            @else
                                                                bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                            @endif
                                                        ">
                                                            {{ $latestTxn->status_label }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-gray-500 dark:text-gray-400 italic">
                                                    <p>No associated customer information</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No items currently in this safe.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
