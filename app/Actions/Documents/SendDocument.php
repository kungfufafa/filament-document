<?php

namespace App\Actions\Documents;

use App\Enums\DocumentActivityType;
use App\Enums\DocumentStatus;
use App\Enums\RecipientRole;
use App\Enums\RecipientStatus;
use App\Models\Document;
use App\Notifications\DocumentSigningInvitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SendDocument
{
    public function __construct(private RecordDocumentActivity $recordActivity) {}

    public function handle(Document $document): Document
    {
        return DB::transaction(function () use ($document): Document {
            $document->loadMissing('recipients');

            foreach ($document->recipients as $recipient) {
                if ($recipient->role === RecipientRole::Signer) {
                    $recipient->update(['status' => RecipientStatus::Pending]);
                } else {
                    $recipient->update(['status' => RecipientStatus::Waiting]);
                }
            }

            $document->update([
                'status' => DocumentStatus::Pending,
                'sent_at' => now(),
            ]);

            $this->recordActivity->handle(
                $document,
                DocumentActivityType::Sent,
                "Sent for signature to {$document->recipients->pluck('name')->join(', ')}.",
                $document->user,
            );

            foreach ($document->recipients as $recipient) {
                Notification::route('mail', $recipient->email)
                    ->notify(new DocumentSigningInvitation($document, $recipient));
            }

            return $document->fresh(['recipients', 'activities']);
        });
    }
}
