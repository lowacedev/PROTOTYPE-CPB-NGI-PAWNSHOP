<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Reports Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Active Loans -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Active Loans</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white text-right">₱{{ number_format($totalActiveLoans, 2) }}</div>
                </div>

                <!-- Total Redeemed -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-purple-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Redeemed</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white text-right">{{ number_format($totalRedeemed) }}</div>
                </div>

                <!-- Total Forfeited -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Forfeited</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white text-right">{{ number_format($totalForfeited) }}</div>
                </div>

                <!-- Total Sales Revenue -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">POS Sales Revenue</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white text-right">₱{{ number_format($totalSalesRevenue, 2) }}</div>
                </div>
            </div>

            <!-- Report Links -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <div class="p-6 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold">Available Reports</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                    <a href="{{ route('reports.transactions') }}" class="p-6 border-b border-r border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition block">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200">Daily Transaction Report</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">View all pawn transactions, loans released, and statuses.</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('reports.payments') }}" class="p-6 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition block">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200">Payment Report</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Track all collected payments (interest, redemptions).</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('reports.sales') }}" class="p-6 border-b border-r border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition block md:border-b-0">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200">POS Sales Report</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">View items sold from the Point of Sale.</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('reports.forfeited') }}" class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition block">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200">Forfeited Items Report</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">List of expired and forfeited items ready for sale.</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
