<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Transaction: {{ $transaction->pawn_ticket_number }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-6">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="loan_amount" :value="__('Loan Amount')" />
                                <x-text-input id="loan_amount" class="block mt-1 w-full" type="number" name="loan_amount" :value="old('loan_amount', $transaction->loan_amount)" step="0.01" required />
                            </div>
                            <div>
                                <x-input-label for="interest_rate" :value="__('Interest Rate (%)')" />
                                <x-text-input id="interest_rate" class="block mt-1 w-full" type="number" name="interest_rate" :value="old('interest_rate', $transaction->interest_rate)" step="0.01" required />
                            </div>
                            <div>
                                <x-input-label for="term_days" :value="__('Term (Days)')" />
                                <x-text-input id="term_days" class="block mt-1 w-full" type="number" name="term_days" :value="old('term_days', $transaction->term_days)" required />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">{{ old('notes', $transaction->notes) }}</textarea>
                        </div>
                        <div class="flex gap-4">
                            <x-primary-button>Update Transaction</x-primary-button>
                            <a href="{{ route('transactions.show', $transaction) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
