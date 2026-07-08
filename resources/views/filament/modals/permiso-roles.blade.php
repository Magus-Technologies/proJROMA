<div class="space-y-3">
    @forelse ($roles as $role)
        <div class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2">
            <div class="flex items-center gap-2">
                <x-heroicon-o-shield-check class="w-5 h-5 text-primary-500" />
                <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($role->nombre) }}</span>
            </div>
            <span class="text-xs rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-1 text-gray-500 dark:text-gray-400">
                {{ $role->guard_name }}
            </span>
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
            Ningún rol tiene asignado este permiso.
        </p>
    @endforelse
</div>
