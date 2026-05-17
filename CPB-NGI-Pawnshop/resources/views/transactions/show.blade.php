<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center hide-on-print">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 tracking-tight flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Transaction Overview
            </h2>
            <div class="flex items-center space-x-3">
                <a href="{{ route('transactions.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-lg font-medium text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Status & Ticket Hero Banner --}}
            @php
                $statusGradient = match($transaction->status) {
                    'active' => 'linear-gradient(to right, #22c55e, #059669)', // green-500 to emerald-600
                    'redeemed' => 'linear-gradient(to right, #3b82f6, #4f46e5)', // blue-500 to indigo-600
                    'renewed' => 'linear-gradient(to right, #facc15, #f97316)', // yellow-400 to orange-500
                    'forfeited' => 'linear-gradient(to right, #ef4444, #e11d48)', // red-500 to rose-600
                    default => 'linear-gradient(to right, #6b7280, #4b5563)' // gray-500 to gray-600
                };
            @endphp
            <div class="mb-8 rounded-2xl shadow-lg overflow-hidden hide-on-print relative" style="background: {{ $statusGradient }};">
                <!-- Decorative background elements -->
                <div class="absolute top-0 rightedit-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-10 -mb-8 w-24 h-24 bg-black opacity-10 rounded-full blur-xl"></div>
                
                <div class="p-8 relative z-10 flex flex-col md:flex-row items-center justify-between text-center md:text-left">
                    <div class="flex-1 md:text-left">
                        <p class="text-white opacity-80 font-medium tracking-wider uppercase text-sm mb-1">Ticket Number</p>
                        <h1 class="text-4xl font-extrabold text-white tracking-tight">{{ $transaction->pawn_ticket_number }}</h1>
                    </div>
                    
                    <div class="flex-1 text-center mt-4 md:mt-0">
                        <span class="inline-block px-6 py-2 rounded-full text-white font-bold tracking-widest text-sm backdrop-blur-md shadow-sm uppercase" style="background-color: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4);">
                            {{ $transaction->status_label }}
                        </span>
                    </div>

                    <div class="flex-1 mt-6 md:mt-0 md:text-right text-white opacity-90">
                        <p class="text-white opacity-80 font-medium tracking-wider uppercase text-sm mb-1">Maturity Date</p>
                        <p class="font-bold text-2xl tracking-tight {{ $transaction->status === 'active' && $transaction->maturity_date < now() ? 'text-red-200 animate-pulse' : 'text-white' }}">
                            {{ $transaction->maturity_date->format('M d, Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Main Content Left Side -->
                <div class="flex-1 space-y-8 hide-on-print">
                    
                    {{-- Customer & Teller Info Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Customer Card -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-start gap-4 hover:shadow-md transition-shadow">
                            <div class="h-14 w-14 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xl flex-shrink-0">
                                {{ substr($transaction->customer->first_name, 0, 1) }}{{ substr($transaction->customer->last_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Customer</p>
                                <a href="{{ route('customers.show', $transaction->customer) }}" class="text-lg font-bold text-gray-900 dark:text-white hover:text-blue-600 transition-colors">
                                    {{ $transaction->customer->full_name }}
                                </a>
                                <div class="flex items-center text-gray-500 text-sm mt-1 gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.036 11.036 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" /></svg>
                                    {{ $transaction->customer->phone_number }}
                                </div>
                            </div>
                        </div>

                        <!-- Teller Card -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-start gap-4 hover:shadow-md transition-shadow">
                            <div class="h-14 w-14 rounded-full bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center text-purple-600 dark:text-purple-400 flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Processed By</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $transaction->user->name }}</p>
                                <p class="text-gray-500 text-sm mt-1">{{ $transaction->transaction_date->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Pawned Items --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
                            Pawned Items
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse($transaction->items as $ti)
                                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl p-5 hover:border-blue-300 dark:hover:border-blue-700 transition-colors group">
                                    <div class="flex justify-between items-start mb-2">
                                        <a href="{{ route('items.show', $ti->item) }}" class="font-bold text-gray-900 dark:text-white text-lg group-hover:text-blue-600 transition-colors line-clamp-1">
                                            {{ $ti->item->name }}
                                        </a>
                                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold px-2 py-1 rounded">Qty: {{ $ti->quantity }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ $ti->item->category->name ?? 'Uncategorized' }} • {{ ucfirst($ti->item->condition) }}</p>
                                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500">Appraised Value</span>
                                        <span class="font-bold text-gray-900 dark:text-white">₱{{ number_format($ti->appraised_value, 2) }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-8 text-gray-500 bg-gray-50 dark:bg-gray-800/50 rounded-xl">No items found.</div>
                            @endforelse
                        </div>
                    </div>
                    
                    @if($transaction->notes)
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-r-xl">
                            <h4 class="text-sm font-bold text-yellow-800 dark:text-yellow-400 uppercase tracking-wider mb-1">Notes</h4>
                            <p class="text-gray-700 dark:text-gray-300">{{ $transaction->notes }}</p>
                        </div>
                    @endif

                    {{-- Payments & Progress --}}
                    @php
                        $principalPaid = 0;
                        foreach($transaction->payments as $pmt) {
                            if ($pmt->payment_type === 'redemption') {
                                $principalPaid += $transaction->loan_amount;
                            } elseif ($pmt->payment_type === 'partial') {
                                $principalPaid += $pmt->amount_paid;
                            }
                        }
                        $progress = $transaction->loan_amount > 0 ? min(100, round(($principalPaid / $transaction->loan_amount) * 100, 2)) : 0;
                    @endphp
                    
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Payment History</h3>
                            <div class="text-right">
                                <span class="text-sm font-medium text-gray-500">Principal Progress</span>
                                <div class="flex items-center gap-3 mt-1">
                                    <div class="w-32 h-2.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <span class="font-bold text-sm {{ $progress == 100 ? 'text-green-600' : 'text-gray-700 dark:text-gray-300' }}">{{ $progress }}%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-0">
                            @if($transaction->payments->count())
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                            <tr class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                <th class="px-6 py-4 font-semibold">Date</th>
                                                <th class="px-6 py-4 font-semibold">Type</th>
                                                <th class="px-6 py-4 font-semibold">Amount</th>
                                                <th class="px-6 py-4 font-semibold">Receipt</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                            @foreach($transaction->payments as $pmt)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                                        {{ $pmt->payment_date->format('M d, Y') }}
                                                        <span class="text-xs text-gray-400 block">{{ $pmt->payment_date->format('h:i A') }}</span>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full 
                                                            {{ $pmt->payment_type === 'interest' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-400' : 
                                                               ($pmt->payment_type === 'redemption' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                                            {{ $pmt->payment_type_label }}
                                                        </span>
                                                        <span class="text-xs text-gray-500 block mt-1 ml-1">{{ ucfirst(str_replace('_',' ',$pmt->payment_method)) }}</span>
                                                    </td>
                                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-gray-100">
                                                        ₱{{ number_format($pmt->amount_paid, 2) }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                                                        @if($pmt->receipt_number)
                                                            <a href="{{ route('transactions.action-receipt', ['transaction' => $transaction->id, 'payment' => $pmt->id]) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition font-semibold" target="_blank" title="View Receipt">
                                                                {{ $pmt->receipt_number }}
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-10 px-6">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400 font-medium">No payments recorded yet.</p>
                                    <p class="text-sm text-gray-400 mt-1">This transaction hasn't received any renewals or redemption payments.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Receipt Right Side (Thermal Look) -->
                <div class="flex-shrink-0 relative" style="width: 100%; max-width: 380px;">
                    

                        <!-- Thermal Receipt CSS Design -->
                        <div class="thermal-receipt bg-white w-full text-gray-800 mx-auto" id="printable-receipt">
                            <div class="p-8">
                                <div class="text-center mb-6 border-b-2 border-dashed border-gray-300 pb-5">
                                    <h1 class="text-2xl font-black uppercase tracking-widest text-gray-900">CPB-NGI PAWNSHOP</h1>
                                    <p class="text-xs text-gray-500 mt-1">Poblacion District 8000 Davao City, Davao Del Sur Philippines</p>
                                     <p class="text-xs text-gray-500 mt-1">Tel. No. 237-3106</p>
                                    
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-widest mb-1">Ticket Number</p>
                                        <p class="font-bold text-lg text-gray-900 tracking-wider">{{ $transaction->pawn_ticket_number }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $transaction->transaction_date->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>

                                <div class="space-y-5 text-sm border-b-2 border-dashed border-gray-300 pb-5">
                                    <div>
                                        <h3 class="font-bold text-xs text-gray-400 uppercase tracking-wider mb-1">Customer</h3>
                                        <p class="font-bold text-gray-800 text-base">{{ $transaction->customer->full_name }}</p>
                                        <p class="text-xs text-gray-600 font-mono">{{ $transaction->customer->phone_number }}</p>
                                    </div>
                                    
                                    <div>
                                        <h3 class="font-bold text-xs text-gray-400 uppercase tracking-wider mb-2">Item Pawned</h3>
                                        @foreach($transaction->items as $txnItem)
                                            <div class="flex justify-between items-start mb-1.5">
                                                <div class="pr-2">
                                                    <span class="font-bold text-gray-800 block">{{ $txnItem->item->name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $txnItem->item->category->name ?? 'Uncategorized' }}</span>
                                                </div>
                                                <span class="font-bold whitespace-nowrap">₱{{ number_format($txnItem->appraised_value, 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="space-y-2 mt-5 text-sm border-b-2 border-dashed border-gray-300 pb-5">
                                    <div class="flex justify-between text-gray-600">
                                        <span>Principal Loan:</span>
                                        <span class="font-bold text-gray-900">₱{{ number_format($transaction->loan_amount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Interest Rate:</span>
                                        <span class="font-bold text-gray-900">{{ $transaction->interest_rate }}%</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Advance Interest:</span>
                                        <span class="font-bold text-gray-900">₱{{ number_format($transaction->calculateInterest(), 2) }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600 pb-2 border-b border-gray-100 mb-2">
                                        <span>Service Charge:</span>
                                        <span class="font-bold text-gray-900">₱5.00</span>
                                    </div>
                                    <div class="flex justify-between items-center mt-3">
                                        <span class="font-bold text-gray-900 uppercase tracking-wider">Net Proceeds:</span>
                                        <span class="font-black text-xl text-gray-900">₱{{ number_format($transaction->loan_amount - $transaction->calculateInterest() - 5, 2) }}</span>
                                    </div>
                                    
                                    <div class="mt-4 pt-4 bg-gray-50 -mx-4 px-4 py-3 rounded-lg flex flex-col items-center border border-gray-200">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Maturity Date</span>
                                        <span class="font-black text-lg text-gray-900">{{ $transaction->maturity_date->format('M d, Y') }}</span>
                                    </div>
                                </div>

                                <div class="text-center mt-6 text-xs text-gray-500 space-y-1">
                                    <p class="font-medium text-gray-700">Please present this ticket when redeeming.</p>
                                    <p>Valid for {{ $transaction->term_days }} days.</p>
                                    <p class="mt-4 pt-2">Served by: <span class="font-bold">{{ $transaction->user->name }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <style>
                    /* Thermal Receipt Styling */
                    .thermal-receipt {
                        position: relative;
                        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                        filter: drop-shadow(0 4px 3px rgba(0,0,0,0.07));
                    }
                    /* Jagged edge top */
                    .thermal-receipt::before {
                        content: "";
                        position: absolute;
                        top: -5px;
                        left: 0;
                        right: 0;
                        height: 5px;
                        background-size: 10px 10px;
                        background-image: radial-gradient(circle at 50% 0, transparent 50%, white 51%);
                    }
                    /* Jagged edge bottom */
                    .thermal-receipt::after {
                        content: "";
                        position: absolute;
                        bottom: -5px;
                        left: 0;
                        right: 0;
                        height: 5px;
                        background-size: 10px 10px;
                        background-image: radial-gradient(circle at 50% 100%, transparent 50%, white 51%);
                    }

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
                            box-shadow: none !important;
                            filter: none !important;
                            margin: 0;
                            padding: 20px;
                        }
                        #printable-receipt::before, #printable-receipt::after {
                            display: none;
                        }
                        .hide-on-print {
                            display: none !important;
                        }
                    }
                </style>
            </div>
        </div>
    </div>
</x-app-layout>

    <!-- Void Transaction Modal -->
    <div x-data="{ 
            voidUrl: '', 
            voidTicket: '', 
            isTeller: false 
        }" 
        @open-void-modal.window="
            voidUrl = $event.detail.url; 
            voidTicket = $event.detail.ticket; 
            isTeller = $event.detail.isTeller;
            $dispatch('open-modal', 'void-transaction-modal');
        ">
        <x-modal name="void-transaction-modal" focusable>
            <form method="POST" x-bind:action="voidUrl" class="p-6">
                @csrf
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    Void Transaction: <span x-text="voidTicket" class="text-yellow-600 dark:text-yellow-400"></span>
                </h2>
                
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" x-show="isTeller">
                    Please provide a reason for this void request. It will be sent to a manager for review.
                </p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" x-show="!isTeller">
                    Are you sure you want to void this transaction? All associated items will also be voided. Please provide a reason.
                </p>

                <div class="mt-6">
                    <x-input-label for="approval_notes" value="Reason for Voiding" class="sr-only" />
                    <textarea
                        id="approval_notes"
                        name="approval_notes"
                        rows="3"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm"
                        placeholder="Enter your reason here..."
                        required
                    ></textarea>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        Cancel
                    </x-secondary-button>

                    <x-danger-button class="ms-3">
                        <span x-show="isTeller">Submit Request</span>
                        <span x-show="!isTeller">Void Immediately</span>
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>
