<x-filament-panels::page>
    @if ($this->getHeaderWidgets())
        <x-filament-widgets::widgets
            :columns="$this->getHeaderWidgetsColumns()"
            :data="$this->getWidgetData()"
            :widgets="$this->getVisibleHeaderWidgets()"
        />
    @endif
</x-filament-panels::page>
