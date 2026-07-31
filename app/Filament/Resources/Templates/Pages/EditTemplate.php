<?php

namespace App\Filament\Resources\Templates\Pages;

use App\Actions\Templates\CreateDocumentFromTemplate;
use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\Templates\TemplateResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditTemplate extends EditRecord
{
    protected static string $resource = TemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('useTemplate')
                ->label('Use template')
                ->icon(Heroicon::OutlinedDocumentPlus)
                ->visible(fn (): bool => $this->getRecord()->deleted_at === null)
                ->action(function (CreateDocumentFromTemplate $createDocumentFromTemplate): void {
                    $document = $createDocumentFromTemplate->handle($this->getRecord());

                    Notification::make()
                        ->title('Document draft created')
                        ->success()
                        ->send();

                    $this->redirect(DocumentResource::getUrl('edit', ['record' => $document]));
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return CreateTemplate::applyFileMetadata($data);
    }
}
