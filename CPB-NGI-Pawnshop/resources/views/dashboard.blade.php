<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Customers -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Customers</div>
                            <div class="text-blue-500 text-3xl">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7Zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216ZM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>
                            </div>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white text-right">{{ $customerCount }}</div>
                    </div>
                </div>

                <!-- Active Transactions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Active Transactions</div>
                            <div class="text-green-500 text-3xl">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-clipboard2-data-fill" viewBox="0 0 16 16"><path d="M10 .5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5.5.5 0 0 1-.5.5.5.5 0 0 0-.5.5V2a.5.5 0 0 0 .5.5h5A.5.5 0 0 0 11 2v-.5a.5.5 0 0 0-.5-.5.5.5 0 0 1-.5-.5Z"/><path d="M4.085 1H3.5A1.5 1.5 0 0 0 2 2.5v12A1.5 1.5 0 0 0 3.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 12.5 1h-.585c.055.156.085.325.085.5V2c0 .828-.668 1.5-1.492 1.5H5.492A1.492 1.492 0 0 1 4 2v-.5c0-.175.03-.344.085-.5ZM10 7a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0V7Zm-3 2a1 1 0 1 1 2 0v3a1 1 0 1 1-2 0V9Zm-3 3a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0v-1Z"/></svg>
                            </div>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white text-right">{{ $activeCount }}</div>
                    </div>
                </div>

                <!-- Total Items -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Items</div>
                            <div class="text-purple-500 text-3xl">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-box-seam-fill" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.556 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184l.01-.003.268-.108a.75.75 0 0 1 .558 0l.269.108.01.003 6.97 2.789ZM10.404 2 4.25 4.461 1.846 3.5 8 1.039 10.404 2ZM16 4.67 8 7.87 0 4.668V12.5l8 3.2 8-3.2V4.67Z"/></svg>
                            </div>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white text-right">{{ $itemsCount }}</div>
                    </div>
                </div>

                <!-- Total Loan Amount -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-500">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Loan Amount</div>
                            <div class="text-yellow-500 text-3xl">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" class="bi bi-cash-stack" viewBox="0 0 16 16"><path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/><path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/></svg>
                            </div>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white text-right">₱{{ number_format($totalLoanAmount, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Recent Transactions</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ticket #</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Maturity</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th></tr></thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($recentTransactions as $txn)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium">{{ $txn->pawn_ticket_number }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $txn->customer->full_name }}</td>
                                        <td class="px-6 py-4 text-sm">{{ number_format($txn->loan_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $txn->maturity_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 text-sm"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full @if($txn->status==='active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @elseif($txn->status==='redeemed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @elseif($txn->status==='renewed') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">{{ $txn->status_label }}</span></td>
                                        <td class="px-6 py-4 text-sm"><a href="{{ route('transactions.show', $txn) }}" class="text-blue-600 dark:text-blue-400">View</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No transactions yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
