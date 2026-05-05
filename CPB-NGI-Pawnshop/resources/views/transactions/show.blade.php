<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Transaction: {{ $transaction->pawn_ticket_number }}</h2>
            <div class="space-x-2">
                @if($transaction->status === 'active')
                    <a href="{{ route('transactions.edit', $transaction) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">Edit</a>
                @endif
                <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">Back</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6 text-gray-900 dark:text-gray-100"><p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Loan Amount</p><p class="text-2xl font-bold">{{ number_format($transaction->loan_amount, 2) }}</p></div></div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6 text-gray-900 dark:text-gray-100"><p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Interest</p><p class="text-2xl font-bold">{{ number_format($transaction->calculateInterest(), 2) }}</p></div></div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"><div class="p-6 text-gray-900 dark:text-gray-100"><p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Total Due</p><p class="text-2xl font-bold">{{ number_format($transaction->total_due, 2) }}</p></div></div>
            </div>
            {{-- Details --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-6">Transaction Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">Customer</p><p class="font-semibold"><a href="{{ route('customers.show', $transaction->customer) }}" class="text-blue-600 dark:text-blue-400">{{ $transaction->customer->full_name }}</a></p></div>
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">Created By</p><p class="font-semibold">{{ $transaction->user->name }}</p></div>
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">Type</p><p class="font-semibold">{{ $transaction->type_label }}</p></div>
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">Interest Rate</p><p class="font-semibold">{{ $transaction->interest_rate }}%</p></div>
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">Transaction Date</p><p class="font-semibold">{{ $transaction->transaction_date->format('M d, Y H:i') }}</p></div>
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">Maturity Date</p><p class="font-semibold">{{ $transaction->maturity_date->format('M d, Y') }}</p></div>
                        <div><p class="text-sm text-gray-500 dark:text-gray-400">Status</p><p class="font-semibold"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full @if($transaction->status==='active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @elseif($transaction->status==='redeemed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @elseif($transaction->status==='renewed') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @elseif($transaction->status==='forfeited') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @elseif($transaction->status==='sold') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">{{ $transaction->status_label }}</span></p></div>
                    </div>
                    @if($transaction->notes)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700"><p class="text-sm text-gray-500 dark:text-gray-400">Notes</p><p class="font-semibold">{{ $transaction->notes }}</p></div>
                    @endif
                    @if($transaction->status === 'active')
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 flex gap-4">
                            <a href="{{ route('transactions.redeem.form', $transaction) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center">Redeem</a>
                            <a href="{{ route('transactions.renew.form', $transaction) }}" class="px-4 py-2 bg-gray-800 dark:bg-gray-600 text-white rounded-lg hover:bg-gray-900 dark:hover:bg-gray-500 transition text-center">Renew</a>
                            <form method="POST" action="{{ route('transactions.forfeit', $transaction) }}" style="display:inline;">@csrf<button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition" onclick="return confirm('Mark as forfeited?')">Forfeit</button></form>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Items --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-6">Pawned Items</h3>
                    @if($transaction->items->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Appraised Value</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Condition</th></tr></thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($transaction->items as $ti)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 text-sm"><a href="{{ route('items.show', $ti->item) }}" class="text-blue-600 dark:text-blue-400">{{ $ti->item->name }}</a></td>
                                            <td class="px-6 py-4 text-sm">{{ $ti->quantity }}</td>
                                            <td class="px-6 py-4 text-sm font-medium">{{ number_format($ti->appraised_value, 2) }}</td>
                                            <td class="px-6 py-4 text-sm">{{ ucfirst($ti->item->condition) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Payments --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Payments</h3>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded"><p class="text-sm text-gray-500 dark:text-gray-400">Total Paid</p><p class="text-xl font-bold">{{ number_format($transaction->total_paid, 2) }}</p></div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded"><p class="text-sm text-gray-500 dark:text-gray-400">Remaining</p><p class="text-xl font-bold">{{ number_format($transaction->remaining_balance, 2) }}</p></div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded"><p class="text-sm text-gray-500 dark:text-gray-400">Progress</p><p class="text-xl font-bold">{{ $transaction->total_due > 0 ? round(($transaction->total_paid / $transaction->total_due) * 100, 2) : 0 }}%</p></div>
                    </div>
                    @if($transaction->payments->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Method</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Receipt</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th></tr></thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($transaction->payments as $pmt)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 text-sm">{{ $pmt->payment_date->format('M d, Y H:i') }}</td>
                                            <td class="px-6 py-4 text-sm font-medium">{{ number_format($pmt->amount_paid, 2) }}</td>
                                            <td class="px-6 py-4 text-sm">{{ $pmt->payment_type_label }}</td>
                                            <td class="px-6 py-4 text-sm">{{ ucfirst(str_replace('_',' ',$pmt->payment_method)) }}</td>
                                            <td class="px-6 py-4 text-sm">{{ $pmt->receipt_number }}</td>
                                            <td class="px-6 py-4 text-sm"><form method="POST" action="{{ route('payments.destroy', $pmt) }}" style="display:inline;">@csrf @method('DELETE')<button type="submit" class="text-red-600 dark:text-red-400" onclick="return confirm('Delete?')">Delete</button></form></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-6">No payments recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
