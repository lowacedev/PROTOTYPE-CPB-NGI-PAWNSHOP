<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Items') }}
            </h2>

                <form method="GET" action="{{ route('items.index') }}" class="flex flex-wrap gap-3 items-center">
                
                    <!-- 🔍 Search -->
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}"
                        placeholder="Search item name or code..."
                        class="px-4 py-2 border rounded-lg w-64 dark:bg-gray-700 dark:text-white"
                    >
                
                    <!-- 📂 Category Filter -->
                    <select name="category" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                
                    <!-- 📊 Status Filter -->
                    <select name="status" class="px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="loaned" {{ request('status') == 'loaned' ? 'selected' : '' }}>Pawned</option>
                    </select>
                
                    <!-- 🔘 Buttons -->
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                        Filter
                    </button>
                
                    <a href="{{ route('items.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg">
                        Reset
                    </a>
                
                </form>
            
        </div>
    </x-slot>


    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($items->count())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Appraised Value</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($items as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $item->item_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->category->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ number_format($item->appraised_value, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($item->condition === 'excellent') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($item->condition === 'good') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @elseif($item->condition === 'fair') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @endif">
                                                {{ ucfirst($item->condition) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($item->item_status === 'for_sale') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($item->item_status === 'stored') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @elseif($item->item_status === 'sold') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                @elseif($item->item_status === 'renewed') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @elseif($item->item_status === 'redeemed') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @endif">
                                                @if($item->item_status === 'for_sale') For Sale
                                                @elseif($item->item_status === 'stored') Pawned
                                                @elseif($item->item_status === 'sold') Sold
                                                @elseif($item->item_status === 'renewed') Renewed
                                                @elseif($item->item_status === 'redeemed') Redeemed
                                                @else {{ ucfirst($item->item_status) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                            <a href="{{ route('items.show', $item) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">View</a>
                                            <a href="{{ route('items.edit', $item) }}" class="text-green-600 hover:text-green-900 dark:text-green-400">Edit</a>
                                            <form method="POST" action="{{ route('items.destroy', $item) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4">
                        {{ $items->links() }}
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                        <p class="text-gray-500 dark:text-gray-400">No items added yet. Items will appear here when a new pawn transaction is created.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
