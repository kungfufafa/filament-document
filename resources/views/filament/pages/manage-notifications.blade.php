<x-filament-panels::page>
    <x-filament::section heading="Email notifications">
        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
            <li class="flex items-start gap-2">
                <x-filament::icon icon="heroicon-o-check-circle" class="mt-0.5 h-5 w-5 text-primary-500" />
                <span>Notify me when a document is viewed by a recipient.</span>
            </li>
            <li class="flex items-start gap-2">
                <x-filament::icon icon="heroicon-o-check-circle" class="mt-0.5 h-5 w-5 text-primary-500" />
                <span>Notify me when a document is signed or completed.</span>
            </li>
            <li class="flex items-start gap-2">
                <x-filament::icon icon="heroicon-o-check-circle" class="mt-0.5 h-5 w-5 text-primary-500" />
                <span>Notify me when a recipient declines a document.</span>
            </li>
            <li class="flex items-start gap-2">
                <x-filament::icon icon="heroicon-o-check-circle" class="mt-0.5 h-5 w-5 text-primary-500" />
                <span>Send reminders before a document expires.</span>
            </li>
            <li class="flex items-start gap-2">
                <x-filament::icon icon="heroicon-o-check-circle" class="mt-0.5 h-5 w-5 text-primary-500" />
                <span>Weekly summary of pending documents and approvals.</span>
            </li>
        </ul>

        <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
            Notification preferences will be configurable in a future release.
        </p>
    </x-filament::section>
</x-filament-panels::page>
