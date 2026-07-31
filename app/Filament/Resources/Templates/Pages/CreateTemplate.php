<?php

namespace App\Filament\Resources\Templates\Pages;

use App\Filament\Resources\Templates\TemplateResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateTemplate extends CreateRecord
{
    protected static string $resource = TemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return self::applyFileMetadata($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function applyFileMetadata(array $data): array
    {
        if (blank($data['file_path'] ?? null)) {
            return $data;
        }

        $disk = Storage::disk(config('filesystems.default'));

        $data['original_filename'] = basename($data['file_path']);
        $data['mime_type'] = $disk->mimeType($data['file_path']) ?: 'application/pdf';
        $data['page_count'] = $data['page_count'] ?? 1;

        return $data;
    }
}
