<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Record Payment') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('payments.store') }}" class="space-y-6">
                        @csrf
                        <div>
                            <x-input-label for="transaction_id" :value="__('Transaction')" />
                            <select id="transaction_id" name="transaction_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                <option value="">Select transaction...</option>
                                @foreach($transactions as $txn)
                                    <option value="{{ $txn->id }}" @selected(($transaction && $transaction->id == $txn->id) || old('transaction_id') == $txn->id)>{{ $txn->pawn_ticket_number }} - {{ $txn->customer->full_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('transaction_id')" class="mt-2" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="amount_paid" :value="__('Amount')" />
                                <x-text-input id="amount_paid" class="block mt-1 w-full" type="number" name="amount_paid" :value="old('amount_paid')" step="0.01" required />
                                <x-input-error :messages="$errors->get('amount_paid')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="payment_type" :value="__('Payment Type')" />
                                <select id="payment_type" name="payment_type" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                    <option value="interest" @selected(old('payment_type')==='interest')>Interest</option>
                                    <option value="redemption" @selected(old('payment_type')==='redemption')>Redemption</option>
                                    <option value="partial" @selected(old('payment_type')==='partial')>Partial</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="payment_method" :value="__('Payment Method')" />
                                <select id="payment_method" name="payment_method" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                    <option value="cash" @selected(old('payment_method')==='cash')>Cash</option>
                                    <option value="check" @selected(old('payment_method')==='check')>Check</option>
                                    <option value="card" @selected(old('payment_method')==='card')>Card</option>
                                    <option value="bank_transfer" @selected(old('payment_method')==='bank_transfer')>Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        </div>
                        <div class="flex gap-4">
                            <x-primary-button>Record Payment</x-primary-button>
                            <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
