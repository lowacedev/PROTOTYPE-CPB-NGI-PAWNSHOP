<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Customer: {{ $customer->full_name }}</h2>
            <div class="space-x-2">
                <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Edit</a>
                <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">Back</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-6">Customer Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">Full Name</p><p class="font-semibold">{{ $customer->full_name }}</p></div>
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">Email</p><p class="font-semibold">{{ $customer->email ?? 'N/A' }}</p></div>
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">Phone</p><p class="font-semibold">{{ $customer->phone_number }}</p></div>
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">ID Type</p><p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $customer->id_type)) }}</p></div>
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">ID Number</p><p class="font-semibold">{{ $customer->id_number }}</p></div>
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">Status</p><p class="font-semibold"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full @if($customer->is_active) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @else bg-gray-100 text-gray-800 @endif">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span></p></div>
                    </div>
                    <div class="mt-6"><p class="text-sm text-gray-500 dark:text-gray-400">Address</p><p class="font-semibold">{{ $customer->full_address }}</p></div>
                    @if($customer->id_image_path)
                        <div class="mt-6"><p class="text-sm text-gray-500 dark:text-gray-400 mb-2">ID Image</p><img src="{{ asset('storage/' . $customer->id_image_path) }}" alt="Customer ID" class="h-40 rounded border dark:border-gray-600"></div>
                    @endif
                    @if($customer->notes)
                        <div class="mt-6"><p class="text-sm text-gray-500 dark:text-gray-400">Notes</p><p class="font-semibold">{{ $customer->notes }}</p></div>
                    @endif
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Transaction History</h3>
                        <a href="{{ route('pawn.wizard') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">+ New Transaction</a>
                    </div>
                    @if($customer->transactions->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ticket #</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Maturity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($customer->transactions as $txn)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 text-sm font-medium">{{ $txn->pawn_ticket_number }}</td>
                                            <td class="px-6 py-4 text-sm">{{ $txn->type_label }}</td>
                                            <td class="px-6 py-4 text-sm">{{ number_format($txn->loan_amount, 2) }}</td>
                                            <td class="px-6 py-4 text-sm">{{ $txn->transaction_date->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 text-sm">{{ $txn->maturity_date->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($txn->status==='active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($txn->status==='redeemed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                    @elseif($txn->status==='renewed') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">{{ $txn->status_label }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-sm"><a href="{{ route('transactions.show', $txn) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">View</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-6">No transactions yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
