<x-filament-panels::page>
    <x-filament::section heading="Connected apps">
        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
            <table class="w-full divide-y divide-gray-200 text-sm dark:divide-white/10">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-950 dark:text-white">Integration</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-950 dark:text-white">Description</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-950 dark:text-white">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach ($this->getIntegrations() as $integration)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-950 dark:text-white">
                                {{ $integration['name'] }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                {{ $integration['description'] }}
                            </td>
                            <td class="px-4 py-3">
                                <x-filament::badge color="gray">
                                    Not connected
                                </x-filament::badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
            Integration connections will be available in a future release.
        </p>
    </x-filament::section>
</x-filament-panels::page>
