<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Create New Pawn Transaction') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6" id="txnForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="customer_id" :value="__('Select Customer')" />
                                <select id="customer_id" name="customer_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                    <option value="">Choose a customer...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" @selected(old('customer_id')==$customer->id)>{{ $customer->full_name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="loan_amount" :value="__('Loan Amount')" />
                                <x-text-input id="loan_amount" class="block mt-1 w-full" type="number" name="loan_amount" :value="old('loan_amount')" step="0.01" required />
                                <x-input-error :messages="$errors->get('loan_amount')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="interest_rate" :value="__('Interest Rate (%)')" />
                                <x-text-input id="interest_rate" class="block mt-1 w-full" type="number" name="interest_rate" :value="old('interest_rate', 5)" step="0.01" required />
                                <x-input-error :messages="$errors->get('interest_rate')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="term_days" :value="__('Term (Days)')" />
                                <x-text-input id="term_days" class="block mt-1 w-full" type="number" name="term_days" :value="old('term_days', 30)" required />
                                <x-input-error :messages="$errors->get('term_days')" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        </div>
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Items to Pawn</h3>
                            <div id="items-container" class="space-y-4 mb-4">
                                <div class="item-row p-4 bg-gray-50 dark:bg-gray-700 rounded-lg" data-row="0">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <x-input-label :value="__('Item')" />
                                            <select name="items[0][item_id]" class="item-select block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                                <option value="">Choose an item...</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}" data-value="{{ $item->appraised_value }}">{{ $item->name }} - {{ number_format($item->appraised_value, 2) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <x-input-label :value="__('Quantity')" />
                                            <x-text-input name="items[0][quantity]" class="block mt-1 w-full" type="number" value="1" min="1" required />
                                        </div>
                                        <div class="flex items-end">
                                            <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition remove-item" style="display:none;">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition" id="add-item">+ Add Another Item</button>
                            <x-input-error :messages="$errors->get('items')" class="mt-2" />
                        </div>
                        <div class="flex gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <x-primary-button>{{ __('Create Transaction') }}</x-primary-button>
                            <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    let itemCount=1;
    document.getElementById('add-item').addEventListener('click',function(){
        const row=document.querySelector('.item-row').cloneNode(true);
        row.setAttribute('data-row',itemCount);
        row.querySelectorAll('select,input').forEach(el=>{const n=el.getAttribute('name');if(n)el.setAttribute('name',n.replace(/\[\d+\]/,`[${itemCount}]`));if(el.tagName==='SELECT')el.value='';else if(el.type==='number')el.value='1';});
        row.querySelector('.remove-item').style.display='block';
        row.querySelector('.remove-item').addEventListener('click',function(){row.remove();updateBtns();});
        document.getElementById('items-container').appendChild(row);
        updateBtns();itemCount++;
    });
    function updateBtns(){const rows=document.querySelectorAll('.item-row');rows.forEach(r=>{r.querySelector('.remove-item').style.display=rows.length>1?'block':'none';});}
    updateBtns();
    </script>
</x-app-layout>
