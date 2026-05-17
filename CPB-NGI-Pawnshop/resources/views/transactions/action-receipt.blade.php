<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center hide-on-print">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Payment Receipt') }}
            </h2>
            <div class="space-x-2">
                <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Print Receipt
                </button>
                <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Back to List of Transactions
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 flex justify-center">
        <!-- Receipt Container (Styled like a thermal receipt) -->
        <div class="bg-white w-full max-w-sm shadow-xl p-6 text-gray-800" id="printable-receipt">
            <div class="text-center mb-6 border-b-2 border-dashed border-gray-300 pb-4">
                <h1 class="text-2xl font-bold uppercase tracking-wider">CPB-NGI Pawnshop</h1>
                <p class="text-sm">Poblacion District 8000 Davao City, Davao Del Sur Philippines</p>
                <p class="text-xs mt-2 font-bold uppercase">{{ $payment->payment_type_label }} RECEIPT</p>
                <p class="text-xs mt-1">Receipt #: <span class="font-bold">{{ $payment->receipt_number }}</span></p>
                <p class="text-xs">Ticket #: <span class="font-bold">{{ $payment->transaction->pawn_ticket_number }}</span></p>
                <p class="text-xs mt-1">{{ $payment->payment_date->format('M d, Y h:i A') }}</p>
            </div>

            <div class="space-y-4 text-sm border-b-2 border-dashed border-gray-300 pb-4">
                <div>
                    <h3 class="font-bold text-xs text-gray-500 uppercase">Customer</h3>
                    <p class="font-semibold">{{ $payment->transaction->customer->full_name }}</p>
                </div>
                
                <div>
                    <h3 class="font-bold text-xs text-gray-500 uppercase">Item Pawned</h3>
                    @foreach($payment->transaction->items as $txnItem)
                        <div class="flex justify-between mt-1">
                            <span>{{ $txnItem->item->name }}</span>
                            <span>₱{{ number_format($txnItem->appraised_value, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-2 mt-4 text-sm border-b-2 border-dashed border-gray-300 pb-4">
                @if($payment->principal_paid > 0)
                <div class="flex justify-between">
                    <span>Principal:</span>
                    <span>₱{{ number_format($payment->principal_paid, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span>Interest:</span>
                    <span>₱{{ number_format($payment->interest_paid, 2) }}</span>
                </div>
                @if($payment->penalty_paid > 0)
                <div class="flex justify-between text-red-600">
                    <span>Penalty:</span>
                    <span>₱{{ number_format($payment->penalty_paid, 2) }}</span>
                </div>
                @endif
                @if($payment->service_charge > 0)
                <div class="flex justify-between">
                    <span>Service Charge:</span>
                    <span>₱{{ number_format($payment->service_charge, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between font-bold text-lg border-t border-dashed border-gray-300 pt-2 mt-2">
                    <span>Amount Paid:</span>
                    <span>₱{{ number_format($payment->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between text-xs text-gray-600 mt-2">
                    <span>Payment Method:</span>
                    <span class="uppercase">{{ $payment->payment_method }}</span>
                </div>
                
                @if($payment->payment_type === 'interest')
                    <div class="flex justify-between mt-4 font-bold text-base text-blue-600">
                        <span>New Maturity Date:</span>
                        <span>{{ $payment->transaction->maturity_date->format('M d, Y') }}</span>
                    </div>
                @elseif($payment->payment_type === 'redemption')
                    <div class="flex justify-between mt-4 font-bold text-base text-green-600">
                        <span>Transaction Status:</span>
                        <span class="uppercase">CLOSED (REDEEMED)</span>
                    </div>
                @endif
            </div>

            <div class="text-center mt-6 text-xs text-gray-500">
                <p>Thank you for your business!</p>
                <p class="mt-4">Served by: {{ $payment->transaction->user->name ?? 'Teller' }}</p>
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
