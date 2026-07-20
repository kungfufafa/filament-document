<?php

namespace Tests\Feature;

use App\Actions\Documents\SendDocument;
use App\Enums\ApprovalStatus;
use App\Enums\ApprovalType;
use App\Enums\ContactVisibility;
use App\Enums\ContractStatus;
use App\Enums\DocumentStatus;
use App\Enums\EmeteraiPurchaseStatus;
use App\Enums\RecipientRole;
use App\Enums\SignatureMethod;
use App\Filament\Pages\EmeteraiDashboard;
use App\Filament\Resources\Approvals\Pages\CreateApproval;
use App\Filament\Resources\Contacts\Pages\CreateContact;
use App\Filament\Resources\Contracts\Pages\CreateContract;
use App\Filament\Resources\CorporateStamps\Pages\CreateCorporateStamp;
use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Resources\Signatures\Pages\CreateSignature;
use App\Filament\Resources\Templates\Pages\CreateTemplate;
use App\Models\Approval;
use App\Models\Contact;
use App\Models\Contract;
use App\Models\CorporateStamp;
use App\Models\Document;
use App\Models\DocumentRecipient;
use App\Models\EmeteraiBalance;
use App\Models\Signature;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelE2ETest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        Storage::fake('local');
        Notification::fake();
    }

    public function test_list_documents_page_loads(): void
    {
        Livewire::test(ListDocuments::class)
            ->assertSuccessful();
    }

    public function test_list_documents_export_action_shows_coming_soon_notification(): void
    {
        Livewire::test(ListDocuments::class)
            ->callAction('export')
            ->assertNotified('Export coming soon');
    }

    public function test_can_create_contact(): void
    {
        Livewire::test(CreateContact::class)
            ->fillForm([
                'name' => 'E2E Contact',
                'email' => 'e2e.contact@example.com',
                'visibility' => ContactVisibility::Personal->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(Contact::class, [
            'user_id' => $this->user->id,
            'email' => 'e2e.contact@example.com',
        ]);
    }

    public function test_can_create_signature(): void
    {
        Livewire::test(CreateSignature::class)
            ->fillForm([
                'method' => SignatureMethod::Type->value,
                'fullname' => 'Test User',
                'initials' => 'TU',
                'is_default' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(Signature::class, [
            'user_id' => $this->user->id,
            'fullname' => 'Test User',
        ]);
    }

    public function test_can_create_corporate_stamp(): void
    {
        Livewire::test(CreateCorporateStamp::class)
            ->fillForm([
                'company_name' => 'Complete Selular',
                'website' => 'https://example.com',
                'color' => '#2563eb',
                'is_default' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(CorporateStamp::class, [
            'user_id' => $this->user->id,
            'company_name' => 'Complete Selular',
        ]);
    }

    public function test_can_create_contract(): void
    {
        Livewire::test(CreateContract::class)
            ->fillForm([
                'title' => 'E2E Contract',
                'counterparty' => 'Partner Co',
                'contract_value' => 1000000,
                'currency' => 'IDR',
                'status' => ContractStatus::Active->value,
                'effective_date' => now()->toDateString(),
                'expiry_date' => now()->addYear()->toDateString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(Contract::class, [
            'user_id' => $this->user->id,
            'title' => 'E2E Contract',
        ]);
    }

    public function test_can_create_approval_with_steps(): void
    {
        Livewire::test(CreateApproval::class)
            ->fillForm([
                'title' => 'E2E Approval',
                'type' => ApprovalType::WithoutDocument->value,
                'message' => 'Please approve',
                'steps' => [
                    [
                        'approver_name' => 'Manager',
                        'approver_email' => 'manager@example.com',
                        'step_order' => 1,
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $approval = Approval::query()->where('title', 'E2E Approval')->first();
        $this->assertNotNull($approval);
        $this->assertSame(ApprovalStatus::Draft, $approval->status);
        $this->assertCount(1, $approval->steps);
    }

    public function test_can_create_template(): void
    {
        $file = UploadedFile::fake()->create('nda.pdf', 100, 'application/pdf');

        Livewire::test(CreateTemplate::class)
            ->fillForm([
                'title' => 'E2E Template',
                'file_path' => $file,
                'sequential_signing' => false,
                'status' => 'active',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(Template::class, [
            'user_id' => $this->user->id,
            'title' => 'E2E Template',
        ]);
    }

    public function test_document_wizard_page_loads_and_send_action_works(): void
    {
        Livewire::test(CreateDocument::class)
            ->assertSuccessful()
            ->assertFormFieldExists('document_file')
            ->assertFormFieldExists('title')
            ->assertFormFieldExists('recipients');

        $document = Document::factory()->for($this->user)->create([
            'status' => DocumentStatus::Draft,
            'title' => 'E2E Document',
        ]);
        DocumentRecipient::factory()->for($document)->create([
            'role' => RecipientRole::Signer,
            'name' => 'Signer One',
            'email' => 'signer1@example.com',
        ]);

        app(SendDocument::class)->handle($document->fresh('recipients'));

        $this->assertSame(DocumentStatus::Pending, $document->fresh()->status);
        $this->assertNotNull($document->fresh()->sent_at);
    }

    public function test_emeterai_dashboard_buy_action_works(): void
    {
        Livewire::test(EmeteraiDashboard::class)
            ->assertSuccessful()
            ->callAction('buyEmeterai', data: [
                'quantity' => 3,
            ])
            ->assertNotified();

        $balance = EmeteraiBalance::forUser($this->user);
        $this->assertSame(3, $balance->available);
        $this->assertDatabaseHas('emeterai_purchases', [
            'user_id' => $this->user->id,
            'quantity' => 3,
            'status' => EmeteraiPurchaseStatus::Completed->value,
        ]);
    }

    public function test_full_signing_flow_end_to_end(): void
    {
        $document = Document::factory()->for($this->user)->create([
            'status' => DocumentStatus::Pending,
            'title' => 'Sign Me',
        ]);
        $recipient = DocumentRecipient::factory()->for($document)->create([
            'role' => RecipientRole::Signer,
        ]);

        $this->get(route('signing.show', $recipient->access_token))
            ->assertOk()
            ->assertSee('Sign Me');

        $this->post(route('signing.sign', $recipient->access_token), [
            'signature' => 'Signer One',
        ])->assertRedirect(route('signing.show', $recipient->access_token));

        $this->assertSame(DocumentStatus::Completed, $document->fresh()->status);
    }
}
