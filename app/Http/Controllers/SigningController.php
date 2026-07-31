<?php

namespace App\Http\Controllers;

use App\Actions\Documents\RecordDocumentActivity;
use App\Actions\Signing\SignDocument;
use App\Enums\DocumentActivityType;
use App\Enums\RecipientStatus;
use App\Models\DocumentRecipient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SigningController extends Controller
{
    public function show(
        DocumentRecipient $recipient,
        RecordDocumentActivity $recordActivity,
    ): View {
        $document = $recipient->document()->with(['fields' => fn ($q) => $q->where('document_recipient_id', $recipient->id)])->firstOrFail();

        if ($recipient->status === RecipientStatus::Pending) {
            $recipient->update([
                'status' => RecipientStatus::Viewed,
                'viewed_at' => now(),
            ]);

            $recordActivity->handle(
                $document,
                DocumentActivityType::Viewed,
                "Viewed by {$recipient->name} ({$recipient->email}).",
                recipient: $recipient,
            );
        }

        return view('signing.show', [
            'recipient' => $recipient->fresh(),
            'document' => $document,
            'fields' => $document->fields,
        ]);
    }

    public function sign(
        Request $request,
        DocumentRecipient $recipient,
        SignDocument $signDocument,
    ): RedirectResponse {
        $validated = $request->validate([
            'signature' => ['required', 'string', 'max:5000'],
            'fields' => ['nullable', 'array'],
            'fields.*.field_id' => ['required', 'integer'],
            'fields.*.value' => ['required', 'string'],
        ]);

        $fieldValues = $validated['fields'] ?? [];
        $fieldValues[] = [
            'field_id' => $recipient->fields()->where('type', 'signature')->value('id') ?? 0,
            'value' => $validated['signature'],
        ];
        $fieldValues = array_values(array_filter($fieldValues, fn (array $row): bool => ($row['field_id'] ?? 0) > 0));

        if ($fieldValues === []) {
            $recipient->fields()->where('type', 'signature')->update([
                'value' => $validated['signature'],
                'filled_at' => now(),
            ]);
        }

        $signDocument->handle($recipient, $fieldValues);

        return redirect()
            ->route('signing.show', $recipient->access_token)
            ->with('status', 'Document signed successfully.');
    }
}
