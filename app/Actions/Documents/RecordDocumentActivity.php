<?php

namespace App\Actions\Documents;

use App\Enums\DocumentActivityType;
use App\Models\Document;
use App\Models\DocumentActivity;
use App\Models\DocumentRecipient;
use App\Models\User;

class RecordDocumentActivity
{
    public function handle(
        Document $document,
        DocumentActivityType $type,
        string $description,
        ?User $user = null,
        ?DocumentRecipient $recipient = null,
        ?array $meta = null,
    ): DocumentActivity {
        return $document->activities()->create([
            'user_id' => $user?->id,
            'document_recipient_id' => $recipient?->id,
            'type' => $type,
            'actor_name' => $user?->name ?? $recipient?->name,
            'actor_email' => $user?->email ?? $recipient?->email,
            'description' => $description,
            'meta' => $meta,
        ]);
    }
}
