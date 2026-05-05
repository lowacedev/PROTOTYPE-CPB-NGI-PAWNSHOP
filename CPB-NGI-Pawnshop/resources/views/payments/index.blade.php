<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Payments') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Receipt #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ticket #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($payments as $pmt)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $pmt->receipt_number }}</td>
                                    <td class="px-6 py-4 text-sm"><a href="{{ route('transactions.show', $pmt->transaction) }}" class="text-blue-600 dark:text-blue-400">{{ $pmt->transaction->pawn_ticket_number }}</a></td>
                                    <td class="px-6 py-4 text-sm">{{ $pmt->transaction->customer->full_name }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">{{ number_format($pmt->amount_paid, 2) }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $pmt->payment_type_label }}</td>
                                    <td class="px-6 py-4 text-sm">{{ ucfirst(str_replace('_',' ',$pmt->payment_method)) }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $pmt->payment_date->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No payments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">{{ $payments->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
