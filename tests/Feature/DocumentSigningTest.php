<?php

namespace Tests\Feature;

use App\Actions\Documents\SendDocument;
use App\Actions\Signing\SignDocument;
use App\Enums\DocumentStatus;
use App\Enums\RecipientRole;
use App\Enums\RecipientStatus;
use App\Models\Document;
use App\Models\DocumentRecipient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DocumentSigningTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_document_invites_recipients_and_sets_pending_status(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $document = Document::factory()->for($user)->create([
            'status' => DocumentStatus::Draft,
        ]);
        DocumentRecipient::factory()->for($document)->create([
            'role' => RecipientRole::Signer,
            'status' => RecipientStatus::Pending,
        ]);

        app(SendDocument::class)->handle($document->fresh('recipients'));

        $this->assertSame(DocumentStatus::Pending, $document->fresh()->status);
        $this->assertNotNull($document->fresh()->sent_at);
        $this->assertDatabaseHas('document_activities', [
            'document_id' => $document->id,
            'type' => 'sent',
        ]);
    }

    public function test_signing_completes_document_when_all_signers_signed(): void
    {
        $user = User::factory()->create();
        $document = Document::factory()->for($user)->create([
            'status' => DocumentStatus::Pending,
        ]);
        $recipient = DocumentRecipient::factory()->for($document)->create([
            'role' => RecipientRole::Signer,
            'status' => RecipientStatus::Viewed,
        ]);

        app(SignDocument::class)->handle($recipient);

        $this->assertSame(RecipientStatus::Signed, $recipient->fresh()->status);
        $this->assertSame(DocumentStatus::Completed, $document->fresh()->status);
    }

    public function test_public_signing_page_is_accessible_with_token(): void
    {
        $document = Document::factory()->create([
            'status' => DocumentStatus::Pending,
        ]);
        $recipient = DocumentRecipient::factory()->for($document)->create([
            'status' => RecipientStatus::Pending,
        ]);

        $this->get(route('signing.show', $recipient->access_token))
            ->assertOk()
            ->assertSee($document->title);
    }
}
