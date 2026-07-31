<?php

namespace App\Actions\Signing;

use App\Actions\Documents\RecordDocumentActivity;
use App\Enums\DocumentActivityType;
use App\Enums\DocumentStatus;
use App\Enums\RecipientRole;
use App\Enums\RecipientStatus;
use App\Models\Document;
use App\Models\DocumentRecipient;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SignDocument
{
    public function __construct(private RecordDocumentActivity $recordActivity) {}

    /**
     * @param  array<int, array{field_id: int, value: string}>  $fieldValues
     */
    public function handle(DocumentRecipient $recipient, array $fieldValues = []): Document
    {
        if ($recipient->role !== RecipientRole::Signer) {
            throw new InvalidArgumentException('Only signers can sign this document.');
        }

        if ($recipient->status === RecipientStatus::Signed) {
            throw new InvalidArgumentException('Recipient has already signed.');
        }

        return DB::transaction(function () use ($recipient, $fieldValues): Document {
            $document = $recipient->document()->lockForUpdate()->firstOrFail();

            foreach ($fieldValues as $fieldValue) {
                $document->fields()
                    ->whereKey($fieldValue['field_id'])
                    ->where('document_recipient_id', $recipient->id)
                    ->update([
                        'value' => $fieldValue['value'],
                        'filled_at' => now(),
                    ]);
            }

            $recipient->update([
                'status' => RecipientStatus::Signed,
                'signed_at' => now(),
            ]);

            $this->recordActivity->handle(
                $document,
                DocumentActivityType::Signed,
                "Signed by {$recipient->name} ({$recipient->email}).",
                recipient: $recipient,
            );

            $unsignedSigners = $document->recipients()
                ->where('role', RecipientRole::Signer)
                ->where('status', '!=', RecipientStatus::Signed)
                ->exists();

            if (! $unsignedSigners) {
                $document->update([
                    'status' => DocumentStatus::Completed,
                    'completed_at' => now(),
                ]);

                $this->recordActivity->handle(
                    $document,
                    DocumentActivityType::Completed,
                    'The document has been completed.',
                );
            }

            return $document->fresh(['recipients', 'fields', 'activities']);
        });
    }
}
