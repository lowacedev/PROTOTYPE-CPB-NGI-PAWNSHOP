<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center hide-on-print">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Transaction Receipt') }}
            </h2>
            <div class="space-x-2">
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Print Receipt
                </button>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 flex justify-center">
        <!-- Receipt Container (Styled like a thermal receipt) -->
        <div class="bg-white w-full max-w-sm shadow-xl p-6 text-gray-800" id="printable-receipt">
            <div class="text-center mb-6 border-b-2 border-dashed border-gray-300 pb-4">
                <h1 class="text-2xl font-bold uppercase tracking-wider">CPB-NGI Pawnshop</h1>
                <p class="text-sm">Bolton Branch</p>
                <p class="text-xs mt-2">Ticket #: <span class="font-bold text-sm">{{ $transaction->pawn_ticket_number }}</span></p>
                <p class="text-xs">{{ $transaction->transaction_date->format('M d, Y h:i A') }}</p>
            </div>

            <div class="space-y-4 text-sm border-b-2 border-dashed border-gray-300 pb-4">
                <div>
                    <h3 class="font-bold text-xs text-gray-500 uppercase">Customer</h3>
                    <p class="font-semibold">{{ $transaction->customer->full_name }}</p>
                    <p class="text-xs">{{ $transaction->customer->phone_number }}</p>
                </div>
                
                <div>
                    <h3 class="font-bold text-xs text-gray-500 uppercase">Item Pawned</h3>
                    @foreach($transaction->items as $txnItem)
                        <div class="flex justify-between mt-1">
                            <span>{{ $txnItem->item->name }} ({{ $txnItem->item->category->name ?? 'Uncategorized' }})</span>
                            <span>₱{{ number_format($txnItem->appraised_value, 2) }}</span>
                        </div>
                        @if($txnItem->item->condition)
                            <p class="text-xs text-gray-500">Condition: {{ ucfirst($txnItem->item->condition) }}</p>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="space-y-2 mt-4 text-sm border-b-2 border-dashed border-gray-300 pb-4">
                <div class="flex justify-between font-bold text-base">
                    <span>Principal Loan:</span>
                    <span>₱{{ number_format($transaction->loan_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Interest Rate:</span>
                    <span>{{ $transaction->interest_rate }}%</span>
                </div>
                <div class="flex justify-between">
                    <span>Interest Amount:</span>
                    <span>₱{{ number_format($transaction->calculateAdvanceInterest(), 2) }}</span>
                </div>
                <div class="flex justify-between border-b-2 border-gray-300 pb-2 mb-2">
                    <span>Service Charge:</span>
                    <span>₱5.00</span>
                </div>
                <div class="flex justify-between mt-2 font-bold text-lg text-green-700">
                    <span>Net Proceeds:</span>
                    <span>₱{{ number_format($transaction->loan_amount - $transaction->calculateAdvanceInterest() - 5, 2) }}</span>
                </div>
                <div class="flex justify-between mt-2 font-bold text-base text-red-600">
                    <span>Maturity Date:</span>
                    <span>{{ $transaction->maturity_date->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="text-center mt-6 text-xs text-gray-500">
                <p>Please present this ticket when redeeming.</p>
                <p>Valid for {{ $transaction->term_days }} days.</p>
                <p class="mt-4">Served by: {{ $transaction->user->name }}</p>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-receipt, #printable-receipt * {
                visibility: visible;
            }
            #printable-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 100%;
                box-shadow: none;
                margin: 0;
                padding: 20px;
            }
            .hide-on-print {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
