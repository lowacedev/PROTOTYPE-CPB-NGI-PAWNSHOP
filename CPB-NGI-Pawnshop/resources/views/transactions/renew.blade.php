<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Renew Transaction: ') }} {{ $transaction->pawn_ticket_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">
                        <!-- Transaction Info -->
                        <div>
                            <h3 class="text-lg font-bold border-b dark:border-gray-700 pb-2 mb-4">Transaction Details</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Customer:</span>
                                    <span class="font-semibold">{{ $transaction->customer->full_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Item(s):</span>
                                    <span class="font-semibold text-right">
                                        @foreach($transaction->items as $txnItem)
                                            {{ $txnItem->item->name }}<br>
                                        @endforeach
                                    </span>
                                </div>
                                <div class="flex justify-between border-t dark:border-gray-700 pt-3">
                                    <span class="text-gray-500 dark:text-gray-400">Principal Loan:</span>
                                    <span class="font-semibold">₱{{ number_format($transaction->loan_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Interest Rate:</span>
                                    <span class="font-semibold">{{ $transaction->interest_rate }}%</span>
                                </div>
                                <div class="flex justify-between text-red-600 dark:text-red-400 font-bold">
                                    <span>Current Maturity Date:</span>
                                    <span>{{ $transaction->maturity_date->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between text-green-600 dark:text-green-400 font-bold border-t dark:border-gray-700 pt-2">
                                    <span>New Maturity Date (After Renewal):</span>
                                    <span>{{ $newMaturityDate->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 border-b border-gray-200 dark:border-gray-700 pb-2 mb-6">Renewal Payment</h3>
                            
                            <div class="mb-6 space-y-3 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                                <div class="flex justify-between items-center text-lg">
                                    <span class="font-bold text-gray-600 dark:text-gray-300">Interest Due ({{ max(1, $transaction->overdue_terms) }} term/s):</span>
                                    <span class="font-bold text-gray-800 dark:text-gray-200">₱{{ number_format($interestDue, 2) }}</span>
                                </div>
                                @if($penaltyDue > 0)
                                <div class="flex justify-between items-center text-lg text-red-600 dark:text-red-400">
                                    <span class="font-bold">Late Penalty (2%/mo):</span>
                                    <span class="font-bold">₱{{ number_format($penaltyDue, 2) }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between items-center text-lg text-gray-600 dark:text-gray-300">
                                    <span class="font-bold">Service Charge:</span>
                                    <span class="font-bold">₱{{ number_format($serviceCharge, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-xl border-t dark:border-gray-700 pt-3 mt-3">
                                    <span class="font-black text-gray-800 dark:text-white">Total Due:</span>
                                    <span class="font-black text-blue-600 dark:text-blue-400">₱{{ number_format($totalDue, 2) }}</span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('transactions.renew.process', $transaction) }}">
                                @csrf
                                <div class="mb-6">
                                    <label for="amount_paid" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount Paid (₱)</label>
                                    <input type="number" name="amount_paid" id="amount_paid" step="0.01" min="{{ $totalDue }}" value="{{ old('amount_paid', $totalDue) }}" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 font-bold text-lg px-4 py-3" required>
                                    @error('amount_paid')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500 mt-2">Strict payment: Amount must be exactly equal to or greater than the total due (₱{{ number_format($totalDue, 2) }}).</p>
                                </div>

                                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <a href="{{ route('transactions.actions.search') }}" class="inline-flex justify-center items-center px-6 py-2.5 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition shadow-sm">Cancel</a>
                                    <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-md">Confirm Renewal</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
