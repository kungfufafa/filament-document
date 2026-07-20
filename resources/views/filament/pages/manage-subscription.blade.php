<x-filament-panels::page>
    <x-filament::section heading="Current plan">
        <div class="rounded-xl border border-gray-200 p-6 dark:border-white/10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-950 dark:text-white">Free</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        Basic document signing with limited templates and storage.
                    </p>
                </div>

                <x-filament::badge color="success" size="lg">
                    Active
                </x-filament::badge>
            </div>

            <ul class="mt-6 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                <li>Up to 3 documents per month</li>
                <li>5 saved templates</li>
                <li>Email notifications</li>
                <li>Standard support</li>
            </ul>
        </div>

        <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
            Paid plans and billing management will be available in a future release.
        </p>
    </x-filament::section>
</x-filament-panels::page>
