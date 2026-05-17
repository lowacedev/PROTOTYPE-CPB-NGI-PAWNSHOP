<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Audit Log Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 grid grid-cols-2 gap-4">
                        <div><p class="text-gray-500 text-sm">Timestamp</p><p class="font-bold">{{ $auditLog->created_at }}</p></div>
                        <div><p class="text-gray-500 text-sm">User</p><p class="font-bold">{{ $auditLog->user->name ?? 'System' }}</p></div>
                        <div><p class="text-gray-500 text-sm">Action</p><p class="font-bold">{{ $auditLog->action_label }}</p></div>
                        <div class="col-span-2"><p class="text-gray-500 text-sm">Description</p><p class="font-bold">{{ $auditLog->description ?? '-' }}</p></div>
                        <div><p class="text-gray-500 text-sm">Model</p><p class="font-bold">{{ class_basename($auditLog->model_type) }} (ID: {{ $auditLog->model_id }})</p></div>
                        <div><p class="text-gray-500 text-sm">IP Address</p><p class="font-bold">{{ $auditLog->ip_address ?? 'N/A' }}</p></div>
                    </div>
                    
                    <h3 class="text-lg font-bold mb-4">Changes</h3>
                    @if(empty($auditLog->changes))
                        <p class="text-gray-500">No changes recorded.</p>
                    @else
                        <div class="bg-gray-100 dark:bg-gray-900 p-4 rounded overflow-x-auto">
                            <pre class="text-sm"><code>{{ json_encode($auditLog->changes, JSON_PRETTY_PRINT) }}</code></pre>
                        </div>
                    @endif
                    
                    <div class="mt-6">
                        <a href="{{ route('audit-logs.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
