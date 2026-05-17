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
                        @if(auth()->user()->isTeller())
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg border border-yellow-200 dark:border-yellow-700">
                            <h3 class="text-sm font-bold text-yellow-800 dark:text-yellow-400 mb-2">Manager Approval Required</h3>
                            <x-input-label for="approval_notes" :value="__('Reason for Edit')" class="text-yellow-800 dark:text-yellow-400" />
                            <textarea id="approval_notes" name="approval_notes" rows="2" required placeholder="Please explain why this transaction needs to be edited..." class="block mt-1 w-full border-yellow-300 dark:border-yellow-600 dark:bg-gray-800 dark:text-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">{{ old('approval_notes') }}</textarea>
                            <p class="mt-1 text-xs text-yellow-600 dark:text-yellow-500">Your edit will be queued for manager review. The transaction will be locked until approved.</p>
                        </div>
                        @endif
                        <div class="flex gap-4">
                            <x-primary-button>Request Approval</x-primary-button>
                            <a href="{{ route('transactions.show', $transaction) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
