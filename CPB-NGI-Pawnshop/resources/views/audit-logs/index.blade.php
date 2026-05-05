<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Audit Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('audit-logs.index') }}" class="mb-6 flex gap-4 flex-wrap">
                        <select name="action" class="border rounded px-4 py-2 dark:bg-gray-700">
                            <option value="">All Actions</option>
                            <option value="created" @selected(request('action')=='created')>Created</option>
                            <option value="updated" @selected(request('action')=='updated')>Updated</option>
                            <option value="deleted" @selected(request('action')=='deleted')>Deleted</option>
                        </select>
                        <select name="model_type" class="border rounded px-4 py-2 dark:bg-gray-700">
                            <option value="">All Models</option>
                            @foreach($modelTypes as $mt)
                                <option value="{{ $mt }}" @selected(request('model_type')==$mt)>{{ class_basename($mt) }}</option>
                            @endforeach
                        </select>
                        <x-text-input name="search" value="{{ request('search') }}" placeholder="Search..." />
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('audit-logs.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Clear</a>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Model</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">View</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($auditLogs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 text-sm">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $log->user->name ?? 'System' }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="px-2 py-1 rounded text-xs
                                                @if($log->action=='created') bg-green-100 text-green-800
                                                @elseif($log->action=='updated') bg-blue-100 text-blue-800
                                                @elseif($log->action=='deleted') bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</td>
                                        <td class="px-6 py-4 text-sm"><a href="{{ route('audit-logs.show', $log) }}" class="text-blue-600">Details</a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-4 text-center text-sm">No audit logs found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $auditLogs->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
