<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Actions\Documents\RecordDocumentActivity;
use App\Actions\Documents\SendDocument;
use App\Enums\DocumentActivityType;
use App\Enums\DocumentStatus;
use App\Enums\FieldType;
use App\Enums\RecipientRole;
use App\Enums\RecipientStatus;
use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Folder;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateDocument extends CreateRecord
{
    use HasWizard;

    protected static string $resource = DocumentResource::class;

    public string $submitMode = 'draft';

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $pendingRecipients = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $pendingFields = [];

    protected function getSteps(): array
    {
        return [
            Step::make('Document')
                ->description('Upload your file and set a title')
                ->icon(Heroicon::OutlinedDocumentArrowUp)
                ->schema([
                    FileUpload::make('document_file')
                        ->label('Document file')
                        ->disk('local')
                        ->directory('documents')
                        ->visibility('private')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ])
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                            if (blank($state)) {
                                return;
                            }

                            $set('title', Str::of(basename($state))->beforeLast('.')->headline()->toString());
                        }),
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                ]),
            Step::make('Recipients')
                ->description('Who needs to sign or receive this document?')
                ->icon(Heroicon::OutlinedUsers)
                ->schema([
                    Repeater::make('recipients')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            Select::make('role')
                                ->options(RecipientRole::class)
                                ->default(RecipientRole::Signer->value)
                                ->required(),
                            TextInput::make('signing_order')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->required(),
                        ])
                        ->columns(2)
                        ->defaultItems(1)
                        ->minItems(1)
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                ]),
            Step::make('Fields')
                ->description('Place signature and input fields on the document')
                ->icon(Heroicon::OutlinedCursorArrowRays)
                ->schema([
                    Repeater::make('fields')
                        ->schema([
                            Select::make('type')
                                ->options(FieldType::class)
                                ->required(),
                            TextInput::make('page')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->required(),
                            Grid::make(4)->schema([
                                TextInput::make('x')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                TextInput::make('y')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                TextInput::make('width')
                                    ->numeric()
                                    ->default(20)
                                    ->required(),
                                TextInput::make('height')
                                    ->numeric()
                                    ->default(8)
                                    ->required(),
                            ]),
                            Select::make('recipient_email')
                                ->label('Recipient')
                                ->options(function (Get $get): array {
                                    return collect($get('../../recipients') ?? [])
                                        ->filter(fn (array $recipient): bool => filled($recipient['email'] ?? null))
                                        ->mapWithKeys(fn (array $recipient): array => [
                                            $recipient['email'] => ($recipient['name'] ?? $recipient['email']).' ('.$recipient['email'].')',
                                        ])
                                        ->all();
                                })
                                ->searchable(),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->defaultItems(0),
                ]),
            Step::make('Folder')
                ->description('Organize your document')
                ->icon(Heroicon::OutlinedFolder)
                ->schema([
                    Select::make('folder_id')
                        ->label('Folder')
                        ->options(fn (): array => Folder::query()
                            ->where('user_id', auth()->id())
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->nullable(),
                ]),
            Step::make('Send')
                ->description('Configure email and delivery options')
                ->icon(Heroicon::OutlinedPaperAirplane)
                ->schema([
                    TextInput::make('email_subject')
                        ->label('Email subject')
                        ->maxLength(255),
                    Textarea::make('message')
                        ->rows(4)
                        ->columnSpanFull(),
                    DateTimePicker::make('expires_at')
                        ->label('Expires at')
                        ->native(false),
                    Toggle::make('sequential_signing')
                        ->label('Sequential signing')
                        ->helperText('Recipients sign one at a time in signing order.'),
                ]),
        ];
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('saveDraft')
                ->label('Save as draft')
                ->color('gray')
                ->action(fn (): mixed => $this->createWithMode('draft')),
            Action::make('send')
                ->label('Send for signature')
                ->action(fn (): mixed => $this->createWithMode('send')),
            $this->getCancelFormAction(),
        ];
    }

    public function createWithMode(string $mode): void
    {
        $this->submitMode = $mode;

        $this->create();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pendingRecipients = $data['recipients'] ?? [];
        $this->pendingFields = $data['fields'] ?? [];

        $data['user_id'] = auth()->id();
        $data['status'] = DocumentStatus::Draft;

        if (filled($data['document_file'] ?? null)) {
            $path = $data['document_file'];
            $disk = Storage::disk('local');

            $data['file_path'] = $path;
            $data['original_filename'] = basename($path);
            $data['mime_type'] = $disk->exists($path) ? ($disk->mimeType($path) ?: null) : null;
            $data['file_size'] = $disk->exists($path) ? $disk->size($path) : 0;
        }

        unset($data['document_file'], $data['recipients'], $data['fields']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $recipientIdsByEmail = [];

        foreach ($this->pendingRecipients as $recipientData) {
            $recipient = $this->record->recipients()->create([
                'name' => $recipientData['name'],
                'email' => $recipientData['email'],
                'role' => $recipientData['role'] ?? RecipientRole::Signer,
                'status' => RecipientStatus::Waiting,
                'signing_order' => (int) ($recipientData['signing_order'] ?? 1),
            ]);

            $recipientIdsByEmail[$recipient->email] = $recipient->id;
        }

        foreach ($this->pendingFields as $fieldData) {
            $recipientEmail = $fieldData['recipient_email'] ?? null;

            $this->record->fields()->create([
                'document_recipient_id' => filled($recipientEmail)
                    ? ($recipientIdsByEmail[$recipientEmail] ?? null)
                    : null,
                'type' => $fieldData['type'] ?? FieldType::Signature,
                'page' => (int) ($fieldData['page'] ?? 1),
                'x' => (float) ($fieldData['x'] ?? 0),
                'y' => (float) ($fieldData['y'] ?? 0),
                'width' => (float) ($fieldData['width'] ?? 20),
                'height' => (float) ($fieldData['height'] ?? 8),
            ]);
        }

        app(RecordDocumentActivity::class)->handle(
            $this->record->fresh(),
            DocumentActivityType::Created,
            'Document created.',
            auth()->user(),
        );

        if ($this->submitMode === 'send') {
            app(SendDocument::class)->handle($this->record->fresh(['recipients']));
        }
    }
}
