<?php

namespace App\Actions\Templates;

use App\Enums\DocumentStatus;
use App\Enums\FieldType;
use App\Models\Document;
use App\Models\Template;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateDocumentFromTemplate
{
    public function handle(Template $template): Document
    {
        return DB::transaction(function () use ($template): Document {
            $filePath = $this->copyTemplateFile($template->file_path);

            $document = Document::create([
                'user_id' => $template->user_id,
                'folder_id' => $template->folder_id,
                'title' => $template->title,
                'file_path' => $filePath,
                'original_filename' => $template->original_filename,
                'mime_type' => $template->mime_type,
                'page_count' => $template->page_count,
                'status' => DocumentStatus::Draft,
                'sequential_signing' => $template->sequential_signing,
            ]);

            foreach ($template->fields ?? [] as $field) {
                if (! is_array($field)) {
                    continue;
                }

                $document->fields()->create([
                    'type' => FieldType::tryFrom($field['type'] ?? '') ?? FieldType::Signature,
                    'page' => (int) ($field['page'] ?? 1),
                    'x' => (float) ($field['x'] ?? 0),
                    'y' => (float) ($field['y'] ?? 0),
                    'width' => (float) ($field['width'] ?? 20),
                    'height' => (float) ($field['height'] ?? 8),
                    'required' => (bool) ($field['required'] ?? true),
                ]);
            }

            return $document->fresh(['fields']);
        });
    }

    private function copyTemplateFile(string $sourcePath): string
    {
        $disk = Storage::disk(config('filesystems.default'));
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
        $destinationPath = 'documents/'.Str::uuid()->toString().($extension ? ".{$extension}" : '');

        if ($disk->exists($sourcePath)) {
            $disk->copy($sourcePath, $destinationPath);
        } else {
            $disk->put($destinationPath, '');
        }

        return $destinationPath;
    }
}
