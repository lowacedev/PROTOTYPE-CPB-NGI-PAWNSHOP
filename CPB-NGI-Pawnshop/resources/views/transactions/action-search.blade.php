<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Renew or Redeem Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            

            <!-- Search Box -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <label for="transaction_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search by Pawn Ticket Number or Customer Name</label>
                <div class="relative">
                    <input type="text" id="transaction_search" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g. PT-2026... or John Doe" autocomplete="off">
                    <div id="search_loading" class="absolute right-3 top-2.5 hidden">
                        <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Search Results</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ticket #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Loan Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Maturity Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="results_body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Type in the search box to find transactions.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('transaction_search');
            const resultsBody = document.getElementById('results_body');
            const loading = document.getElementById('search_loading');
            let timeout = null;

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    resultsBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">Type in the search box to find transactions.</td></tr>';
                    return;
                }

                loading.classList.remove('hidden');

                timeout = setTimeout(() => {
                    fetch(`/api/transactions-actions/search?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            loading.classList.add('hidden');
                            resultsBody.innerHTML = '';

                            if (data.length === 0) {
                                resultsBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No active transactions found.</td></tr>';
                                return;
                            }

                            data.forEach(txn => {
                                // Formatting dates
                                const maturity = new Date(txn.maturity_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                                
                                // Status styling
                                let statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                if (txn.status === 'active') statusClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                else if (txn.status === 'renewed') statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';

                                const tr = document.createElement('tr');
                                tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
                                tr.innerHTML = `
                                    <td class="px-6 py-4 text-sm font-semibold dark:text-gray-200">${txn.pawn_ticket_number}</td>
                                    <td class="px-6 py-4 text-sm dark:text-gray-200">${txn.customer.first_name} ${txn.customer.last_name}</td>
                                    <td class="px-6 py-4 text-sm dark:text-gray-200">₱${parseFloat(txn.loan_amount).toFixed(2)}</td>
                                    <td class="px-6 py-4 text-sm dark:text-gray-200">${maturity}</td>
                                    <td class="px-6 py-4 text-sm"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${txn.status.toUpperCase()}</span></td>
                                    <td class="px-6 py-4 text-sm font-medium space-x-2">
                                        ${(txn.status === 'active' || txn.status === 'renewed') ? `
                                            <a href="/transactions/${txn.id}/renew" class="text-white bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded text-xs transition">Renew</a>
                                            <a href="/transactions/${txn.id}/redeem" class="text-white bg-blue-600 hover:bg-blue-700 px-3 py-1 rounded text-xs transition">Redeem</a>
                                        ` : `<span class="text-gray-400 text-xs italic">Not available</span>`}
                                    </td>
                                `;
                                resultsBody.appendChild(tr);
                            });
                        })
                        .catch(err => {
                            loading.classList.add('hidden');
                            resultsBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-sm text-red-500">Error fetching data.</td></tr>';
                        });
                }, 300);
            });
        });
    </script>
</x-app-layout>
