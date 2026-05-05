<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight tracking-tight">
            {{ __('Point of Sale') }}
        </h2>
    </x-slot>

    {{-- Main POS Layout: Full height, separate scrolling areas --}}
    <div class="py-6 px-4 sm:px-6 lg:px-8 h-[calc(100vh-100px)] flex flex-col max-w-[1400px] mx-auto">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 flex items-center bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm">
                @foreach($errors->all() as $error)
                    <p class="font-medium flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6 flex-1 min-h-0">
            
            {{-- ═══════════════ LEFT: Items Panel ═══════════════ --}}
            <div class="flex-1 flex flex-col min-w-0 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                {{-- Header + Search --}}
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 z-10">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center tracking-tight">
                                Items For Sale
                                <span class="ml-3 bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-md">{{ $items->count() }}</span>
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select items to add to the current transaction.</p>
                        </div>
                        <div class="relative w-full sm:w-80">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="itemSearch" placeholder="Search by name, code, or category..." 
                                class="block w-full pl-10 pr-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all placeholder-gray-400">
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="flex-1 overflow-y-auto bg-gray-50/30 dark:bg-gray-800/50 relative">
                    @if($items->count())
                        <table class="w-full text-left" id="itemsTable">
                            <thead class="sticky top-0 bg-gray-50/95 dark:bg-gray-700/95 backdrop-blur-sm z-10 shadow-sm border-b border-gray-200 dark:border-gray-600">
                                <tr class="text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <th class="px-6 py-4">Item Details</th>
                                    <th class="px-6 py-4">Category</th>
                                    <th class="px-6 py-4 text-right">Price</th>
                                    <th class="px-6 py-4 text-center w-32">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50 bg-white dark:bg-gray-800">
                                @foreach($items as $item)
                                    <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-colors item-row group"
                                        data-name="{{ strtolower($item->name) }}" 
                                        data-code="{{ strtolower($item->item_code) }}"
                                        data-category="{{ strtolower($item->category->name ?? '') }}">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-900 dark:text-gray-100 text-base mb-1">{{ $item->name }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-mono bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded w-max">{{ $item->item_code }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 shadow-sm">
                                                {{ $item->category->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-black text-gray-900 dark:text-white text-lg tracking-tight">₱{{ number_format($item->appraised_value, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <button type="button" 
                                                onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->appraised_value }})"
                                                class="inline-flex items-center justify-center w-full px-4 py-2 bg-white dark:bg-gray-700 border-2 border-blue-600 dark:border-blue-500 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-500 dark:hover:text-white rounded-xl text-sm font-bold transition-all shadow-sm group-hover:shadow-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="noSearchResults" class="hidden flex-col items-center justify-center py-20 text-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">No items found</h3>
                            <p class="text-sm text-gray-500 mt-1">Try adjusting your search criteria.</p>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-full p-12 text-center">
                            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No items available</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 max-w-sm">There are currently no items marked for sale. Click "Check Expired" to forfeit overdue items.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ═══════════════ RIGHT: Cart Panel ═══════════════ --}}
            <div class="w-full lg:w-[420px] xl:w-[460px] flex-shrink-0 flex flex-col">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col h-full overflow-hidden">
                    
                    {{-- Cart Header --}}
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/80">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                                <span class="bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400 p-1.5 rounded-lg mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                                    </svg>
                                </span>
                                Current Sale
                            </h3>
                            <button type="button" id="clearCartBtn" onclick="clearCart()" class="hidden text-sm text-gray-400 hover:text-red-500 font-semibold transition-colors flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Clear All
                            </button>
                        </div>
                    </div>

                    {{-- Cart Items List --}}
                    <div class="flex-1 overflow-y-auto bg-white dark:bg-gray-800 p-2 relative">
                        <div id="cartItems" class="space-y-2 pb-4">
                            <div class="flex flex-col items-center justify-center h-full py-20 px-6 absolute inset-0" id="emptyCartMsg">
                                <div class="w-24 h-24 rounded-full bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center mb-5 border-2 border-dashed border-gray-200 dark:border-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-gray-500 dark:text-gray-400">Cart is empty</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2 text-center">Select items from the left panel to add them to the transaction.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Cart Footer / Totals --}}
                    <div class="bg-gray-900 dark:bg-gray-950 text-white rounded-b-2xl mt-auto z-10 shadow-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-400 font-medium text-sm">Subtotal (<span id="cartItemCount">0</span> items)</span>
                                <span class="text-gray-300 font-semibold" id="cartSubtotal">₱0.00</span>
                            </div>
                            <div class="flex items-end justify-between mt-4 mb-6 border-t border-gray-700 pt-4">
                                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Total Due</p>
                                <p class="text-4xl font-black text-white tracking-tight flex items-baseline">
                                    <span class="text-2xl mr-1 text-green-400">₱</span>
                                    <span id="cartTotal">0.00</span>
                                </p>
                            </div>
                            <button type="button" id="checkoutBtn" onclick="openCheckoutModal()" disabled
                                class="w-full py-4 bg-green-500 hover:bg-green-400 text-gray-900 font-black rounded-xl focus:ring-4 focus:ring-green-500/30 transition-all disabled:opacity-30 disabled:bg-gray-700 disabled:text-gray-500 disabled:cursor-not-allowed flex items-center justify-center text-lg shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                Proceed to Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ═══════════════ Checkout Modal ═══════════════ --}}
    <div id="checkoutModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden overflow-y-auto h-full w-full flex items-center justify-center transition-opacity" style="z-index: 100;">
        <div class="relative mx-4 p-8 shadow-2xl rounded-3xl bg-white dark:bg-gray-800 w-full max-w-md transform transition-all scale-100">
            
            {{-- Close Button --}}
            <button type="button" onclick="closeCheckoutModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 bg-gray-100 dark:bg-gray-700 p-2 rounded-full transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-6">Complete Payment</h3>

            {{-- Total Display --}}
            <div class="text-center bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-6 mb-6 border border-gray-100 dark:border-gray-600">
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2">Total Amount</p>
                <p class="text-4xl font-black text-green-600 dark:text-green-400 flex justify-center items-baseline">
                    <span class="text-2xl mr-1">₱</span>
                    <span id="modalTotal">0.00</span>
                </p>
            </div>

            <form id="checkoutForm" method="POST" action="{{ route('pos.sell') }}">
                @csrf
                <div id="hiddenItemInputs"></div>

                {{-- Amount Tendered --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2" for="amount_tendered">
                        Cash Tendered
                    </label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-5 flex items-center text-gray-400 font-black text-xl pointer-events-none group-focus-within:text-green-500 transition-colors">₱</span>
                        <input type="number" step="0.01" id="amount_tendered" name="amount_tendered" required autocomplete="off" placeholder="0.00"
                            class="block w-full pl-12 pr-5 py-4 text-2xl font-black border-2 border-gray-200 dark:border-gray-600 rounded-2xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all">
                    </div>
                </div>

                {{-- Change Display --}}
                <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-5 mb-8 border border-gray-100 dark:border-gray-600">
                    <span class="text-base font-bold text-gray-500 dark:text-gray-400">Change</span>
                    <span class="text-2xl font-black" id="changeAmount">₱0.00</span>
                </div>

                {{-- Submit --}}
                <button type="submit" id="confirmCheckoutBtn" disabled
                    class="w-full py-4.5 bg-green-600 hover:bg-green-700 text-white font-black text-lg rounded-2xl transition-all disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center shadow-lg hover:shadow-green-600/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Confirm & Print Receipt
                </button>
            </form>
        </div>
    </div>

    <script>
        // ─── Search ────────────────────────────────────────
        document.getElementById('itemSearch').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.item-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const name = row.dataset.name || '';
                const code = row.dataset.code || '';
                const category = row.dataset.category || '';
                const match = name.includes(query) || code.includes(query) || category.includes(query);
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            const noResults = document.getElementById('noSearchResults');
            if (noResults) {
                noResults.style.display = (visibleCount === 0 && query.length > 0) ? 'flex' : 'none';
                
                // Hide table header if no results
                const thead = document.querySelector('#itemsTable thead');
                if (thead) {
                    thead.style.display = visibleCount === 0 ? 'none' : '';
                }
            }
        });

        // ─── Cart Logic ────────────────────────────────────
        let cart = [];

        function addToCart(id, name, value) {
            if (cart.find(item => item.id === id)) {
                // Shake the item in cart to show it's already there
                const existingItem = document.getElementById(`cart-item-${id}`);
                if(existingItem) {
                    existingItem.classList.add('animate-pulse', 'bg-blue-50', 'dark:bg-gray-700');
                    setTimeout(() => existingItem.classList.remove('animate-pulse', 'bg-blue-50', 'dark:bg-gray-700'), 500);
                }
                return;
            }
            cart.push({ id, name, value });
            renderCart();
            
            // Scroll cart to bottom
            const cartItemsDiv = document.getElementById('cartItems').parentElement;
            setTimeout(() => cartItemsDiv.scrollTop = cartItemsDiv.scrollHeight, 50);
        }

        function removeFromCart(id) {
            const itemElement = document.getElementById(`cart-item-${id}`);
            if(itemElement) {
                itemElement.classList.add('opacity-0', 'scale-95');
                setTimeout(() => {
                    cart = cart.filter(item => item.id !== id);
                    renderCart();
                }, 200);
            } else {
                cart = cart.filter(item => item.id !== id);
                renderCart();
            }
        }

        function clearCart() {
            if (cart.length === 0) return;
            if (!confirm('Remove all items from the cart?')) return;
            cart = [];
            renderCart();
        }

        function renderCart() {
            const cartItemsDiv = document.getElementById('cartItems');
            const checkoutBtn = document.getElementById('checkoutBtn');
            const clearCartBtn = document.getElementById('clearCartBtn');
            const cartItemCount = document.getElementById('cartItemCount');
            const emptyCartMsg = document.getElementById('emptyCartMsg');

            if (cart.length === 0) {
                clearCartBtn.classList.add('hidden');
                cartItemsDiv.innerHTML = '';
                cartItemsDiv.appendChild(emptyCartMsg);
                emptyCartMsg.style.display = 'flex';
                
                document.getElementById('cartTotal').innerText = '0.00';
                document.getElementById('cartSubtotal').innerText = '₱0.00';
                cartItemCount.innerText = '0';
                checkoutBtn.disabled = true;
                return;
            }

            if(emptyCartMsg) emptyCartMsg.style.display = 'none';
            clearCartBtn.classList.remove('hidden');
            cartItemCount.innerText = cart.length;

            let html = '';
            let total = 0;

            cart.forEach((item, index) => {
                total += item.value;
                html += `
                    <div id="cart-item-${item.id}" class="flex items-center gap-3 p-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl hover:shadow-md transition-all group relative overflow-hidden">
                        <div class="absolute inset-y-0 left-0 w-1 bg-green-500 rounded-l-xl"></div>
                        <div class="flex-1 min-w-0 pl-2">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">${item.name}</p>
                            <p class="text-xs text-gray-500 font-mono mt-0.5">Item #${item.id}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-sm font-black text-gray-900 dark:text-white">₱${item.value.toLocaleString('en-PH', {minimumFractionDigits: 2})}</span>
                            <button onclick="removeFromCart(${item.id})" class="text-xs font-semibold text-gray-400 hover:text-red-500 transition-colors flex items-center">
                                Remove
                            </button>
                        </div>
                    </div>`;
            });

            cartItemsDiv.innerHTML = html;
            const formattedTotal = total.toLocaleString('en-PH', {minimumFractionDigits: 2});
            document.getElementById('cartTotal').innerText = formattedTotal;
            document.getElementById('cartSubtotal').innerText = '₱' + formattedTotal;
            checkoutBtn.disabled = false;
        }

        // ─── Checkout Modal ────────────────────────────────
        function openCheckoutModal() {
            const total = cart.reduce((sum, item) => sum + item.value, 0);
            document.getElementById('modalTotal').innerText = total.toLocaleString('en-PH', {minimumFractionDigits: 2});

            let hiddenInputsHtml = '';
            cart.forEach(item => {
                hiddenInputsHtml += `<input type="hidden" name="item_ids[]" value="${item.id}">`;
            });
            document.getElementById('hiddenItemInputs').innerHTML = hiddenInputsHtml;

            document.getElementById('amount_tendered').value = '';
            document.getElementById('changeAmount').innerText = '₱0.00';
            document.getElementById('changeAmount').className = 'text-2xl font-black text-gray-300';
            document.getElementById('confirmCheckoutBtn').disabled = true;

            const modal = document.getElementById('checkoutModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('amount_tendered').focus();
            }, 100);
        }

        function closeCheckoutModal() {
            document.getElementById('checkoutModal').classList.add('hidden');
        }

        document.getElementById('amount_tendered').addEventListener('input', function(e) {
            const total = cart.reduce((sum, item) => sum + item.value, 0);
            const tendered = parseFloat(e.target.value);

            if (isNaN(tendered) || tendered <= 0) {
                document.getElementById('changeAmount').innerText = '₱0.00';
                document.getElementById('changeAmount').className = 'text-2xl font-black text-gray-300';
                document.getElementById('confirmCheckoutBtn').disabled = true;
                return;
            }

            const change = tendered - total;

            if (change < 0) {
                document.getElementById('changeAmount').innerText = 'Insufficient';
                document.getElementById('changeAmount').className = 'text-2xl font-black text-red-500';
                document.getElementById('confirmCheckoutBtn').disabled = true;
            } else {
                document.getElementById('changeAmount').innerText = '₱' + change.toLocaleString('en-PH', {minimumFractionDigits: 2});
                document.getElementById('changeAmount').className = 'text-2xl font-black text-green-500';
                document.getElementById('confirmCheckoutBtn').disabled = false;
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape' && !document.getElementById('checkoutModal').classList.contains('hidden')) {
                closeCheckoutModal();
            }
        });
    </script>
</x-app-layout>
