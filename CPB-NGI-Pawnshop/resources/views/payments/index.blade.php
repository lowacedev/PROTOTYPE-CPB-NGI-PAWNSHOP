<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Payments') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Search & Filter Bar -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-1 relative">
                        <label for="payment_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search by Customer Name or Pawn Ticket</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" id="payment_search" class="block w-full pl-10 pr-10 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g. PT-2026... or John Doe" autocomplete="off">
                            <div id="search_loading" class="absolute right-3 top-2.5 hidden">
                                <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                        </div>
                    </div>
                    <!-- Filter by Type -->
                    <div class="w-full md:w-48">
                        <label for="filter_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Type</label>
                        <select id="filter_type" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="all">All Types</option>
                            <option value="interest">Interest</option>
                            <option value="redemption">Redemption</option>
                            <option value="partial">Partial</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4" id="results_title">Payment Records</h3>
                    
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="results_body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($payments as $pmt)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 server-row">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $pmt->receipt_number }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <a href="{{ route('transactions.show', $pmt->transaction) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $pmt->transaction->pawn_ticket_number }}</a>
                                        </td>
                                        <td class="px-6 py-4 text-sm dark:text-gray-200">{{ $pmt->transaction->customer->full_name }}</td>
                                        <td class="px-6 py-4 text-sm font-semibold dark:text-gray-200">₱{{ number_format($pmt->amount_paid, 2) }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            @php
                                                $typeClasses = match($pmt->payment_type) {
                                                    'interest' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'redemption' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                    'partial' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                };
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeClasses }}">{{ $pmt->payment_type_label }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm dark:text-gray-200">{{ ucfirst(str_replace('_',' ',$pmt->payment_method)) }}</td>
                                        <td class="px-6 py-4 text-sm dark:text-gray-200">{{ $pmt->payment_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <a href="{{ route('transactions.show', $pmt->transaction) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium">View Transaction</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="server-row">
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No payments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-0 py-4" id="pagination_links">{{ $payments->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('payment_search');
            const filterType = document.getElementById('filter_type');
            const resultsBody = document.getElementById('results_body');
            const resultsTitle = document.getElementById('results_title');
            const paginationLinks = document.getElementById('pagination_links');
            const loading = document.getElementById('search_loading');
            let timeout = null;

            function performSearch() {
                clearTimeout(timeout);
                const query = searchInput.value.trim();
                const type = filterType.value;

                // If no search query and no filter, show the original server-rendered data
                if (query.length < 2 && type === 'all') {
                    // Reload to show all
                    document.querySelectorAll('.server-row').forEach(r => r.style.display = '');
                    document.querySelectorAll('.ajax-row').forEach(r => r.remove());
                    paginationLinks.style.display = '';
                    resultsTitle.textContent = 'Payment Records';
                    return;
                }

                loading.classList.remove('hidden');

                timeout = setTimeout(() => {
                    let url = `/api/payments/search?q=${encodeURIComponent(query)}`;
                    if (type !== 'all') {
                        url += `&type=${encodeURIComponent(type)}`;
                    }

                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            loading.classList.add('hidden');
                            // Hide server rows and pagination
                            document.querySelectorAll('.server-row').forEach(r => r.style.display = 'none');
                            document.querySelectorAll('.ajax-row').forEach(r => r.remove());
                            paginationLinks.style.display = 'none';

                            resultsTitle.textContent = `Search Results (${data.length})`;

                            if (data.length === 0) {
                                const tr = document.createElement('tr');
                                tr.className = 'ajax-row';
                                tr.innerHTML = '<td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No payments found matching your search.</td>';
                                resultsBody.appendChild(tr);
                                return;
                            }

                            data.forEach(pmt => {
                                // Type badge color
                                let typeClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                const typeLower = pmt.payment_type.toLowerCase();
                                if (typeLower === 'interest') typeClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                                else if (typeLower === 'redemption') typeClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                                else if (typeLower === 'partial') typeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';

                                const tr = document.createElement('tr');
                                tr.className = 'hover:bg-gray-50 dark:hover:bg-gray-700 ajax-row';
                                tr.innerHTML = `
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">${pmt.receipt_number}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="/transactions/${pmt.transaction_id}" class="text-blue-600 dark:text-blue-400 hover:underline">${pmt.pawn_ticket_number}</a>
                                    </td>
                                    <td class="px-6 py-4 text-sm dark:text-gray-200">${pmt.customer_name}</td>
                                    <td class="px-6 py-4 text-sm font-semibold dark:text-gray-200">₱${pmt.amount_paid}</td>
                                    <td class="px-6 py-4 text-sm"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${typeClass}">${pmt.payment_type}</span></td>
                                    <td class="px-6 py-4 text-sm dark:text-gray-200">${pmt.payment_method}</td>
                                    <td class="px-6 py-4 text-sm dark:text-gray-200">${pmt.payment_date}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="/transactions/${pmt.transaction_id}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium">View Transaction</a>
                                    </td>
                                `;
                                resultsBody.appendChild(tr);
                            });
                        })
                        .catch(err => {
                            loading.classList.add('hidden');
                            document.querySelectorAll('.server-row').forEach(r => r.style.display = 'none');
                            document.querySelectorAll('.ajax-row').forEach(r => r.remove());
                            const tr = document.createElement('tr');
                            tr.className = 'ajax-row';
                            tr.innerHTML = '<td colspan="8" class="px-6 py-4 text-center text-sm text-red-500">Error fetching data.</td>';
                            resultsBody.appendChild(tr);
                        });
                }, 300);
            }

            searchInput.addEventListener('input', performSearch);
            filterType.addEventListener('change', performSearch);
        });
    </script>
</x-app-layout>
