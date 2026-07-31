<?php

namespace Tests\Feature;

use App\Enums\ApprovalStatus;
use App\Enums\ApprovalType;
use App\Enums\ContactVisibility;
use App\Enums\ContractStatus;
use App\Enums\DocumentStatus;
use App\Enums\EmeteraiPurchaseStatus;
use App\Enums\RecipientRole;
use App\Enums\SignatureMethod;
use App\Enums\TemplateStatus;
use App\Filament\Pages\EmeteraiDashboard;
use App\Filament\Pages\ManageIntegrations;
use App\Filament\Pages\ManageNotifications;
use App\Filament\Pages\ManageSubscription;
use App\Filament\Resources\Approvals\Pages\CreateApproval;
use App\Filament\Resources\Approvals\Pages\EditApproval;
use App\Filament\Resources\Approvals\Pages\ListApprovals;
use App\Filament\Resources\ContactGroups\Pages\CreateContactGroup;
use App\Filament\Resources\ContactGroups\Pages\EditContactGroup;
use App\Filament\Resources\ContactGroups\Pages\ListContactGroups;
use App\Filament\Resources\Contacts\Pages\CreateContact;
use App\Filament\Resources\Contacts\Pages\EditContact;
use App\Filament\Resources\Contacts\Pages\ListContacts;
use App\Filament\Resources\Contracts\Pages\CreateContract;
use App\Filament\Resources\Contracts\Pages\EditContract;
use App\Filament\Resources\Contracts\Pages\ListContracts;
use App\Filament\Resources\CorporateStamps\Pages\CreateCorporateStamp;
use App\Filament\Resources\CorporateStamps\Pages\EditCorporateStamp;
use App\Filament\Resources\CorporateStamps\Pages\ListCorporateStamps;
use App\Filament\Resources\Documents\Pages\CreateDocument;
use App\Filament\Resources\Documents\Pages\EditDocument;
use App\Filament\Resources\Documents\Pages\ListDocuments;
use App\Filament\Resources\Documents\Pages\ViewDocument;
use App\Filament\Resources\EmeteraiUsages\Pages\ListEmeteraiUsages;
use App\Filament\Resources\Folders\Pages\ManageFolders;
use App\Filament\Resources\Signatures\Pages\CreateSignature;
use App\Filament\Resources\Signatures\Pages\EditSignature;
use App\Filament\Resources\Signatures\Pages\ListSignatures;
use App\Filament\Resources\Templates\Pages\CreateTemplate;
use App\Filament\Resources\Templates\Pages\EditTemplate;
use App\Filament\Resources\Templates\Pages\ListTemplates;
use App\Models\Approval;
use App\Models\ApprovalStep;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Contract;
use App\Models\CorporateStamp;
use App\Models\Document;
use App\Models\DocumentRecipient;
use App\Models\EmeteraiBalance;
use App\Models\Folder;
use App\Models\Signature;
use App\Models\Template;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Pages\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RuntimeSmokeTest extends TestCase
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

    public function test_all_admin_get_routes_return_ok(): void
    {
        $folder = Folder::factory()->for($this->user)->create();
        $document = Document::factory()->for($this->user)->create([
            'status' => DocumentStatus::Draft,
            'folder_id' => $folder->id,
        ]);
        DocumentRecipient::factory()->for($document)->create();
        $contact = Contact::factory()->for($this->user)->create();
        $contactGroup = ContactGroup::factory()->for($this->user)->create();
        $template = Template::factory()->for($this->user)->create();
        $approval = Approval::factory()->for($this->user)->create();
        $contract = Contract::factory()->for($this->user)->create();
        $signature = Signature::factory()->for($this->user)->create();
        $stamp = CorporateStamp::factory()->for($this->user)->create();

        $routes = [
            '/admin',
            '/admin/documents',
            '/admin/documents/create',
            "/admin/documents/{$document->id}",
            "/admin/documents/{$document->id}/edit",
            '/admin/folders',
            '/admin/approvals',
            '/admin/approvals/create',
            "/admin/approvals/{$approval->id}/edit",
            '/admin/contracts',
            '/admin/contracts/create',
            "/admin/contracts/{$contract->id}/edit",
            '/admin/templates',
            '/admin/templates/create',
            "/admin/templates/{$template->id}/edit",
            '/admin/emeterai-dashboard',
            '/admin/emeterai-usages',
            '/admin/contacts',
            '/admin/contacts/create',
            "/admin/contacts/{$contact->id}/edit",
            '/admin/contact-groups',
            '/admin/contact-groups/create',
            "/admin/contact-groups/{$contactGroup->id}/edit",
            '/admin/signatures',
            '/admin/signatures/create',
            "/admin/signatures/{$signature->id}/edit",
            '/admin/corporate-stamps',
            '/admin/corporate-stamps/create',
            "/admin/corporate-stamps/{$stamp->id}/edit",
            '/admin/manage-notifications',
            '/admin/manage-integrations',
            '/admin/manage-subscription',
            '/admin/profile',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $this->assertSame(
                200,
                $response->getStatusCode(),
                "Expected OK for {$route}, got {$response->getStatusCode()}: ".substr(strip_tags($response->getContent()), 0, 300),
            );
        }
    }

    public function test_all_list_and_settings_livewire_pages_load(): void
    {
        $pages = [
            Dashboard::class,
            ListDocuments::class,
            CreateDocument::class,
            ManageFolders::class,
            ListApprovals::class,
            CreateApproval::class,
            ListContracts::class,
            CreateContract::class,
            ListTemplates::class,
            CreateTemplate::class,
            EmeteraiDashboard::class,
            ListEmeteraiUsages::class,
            ListContacts::class,
            CreateContact::class,
            ListContactGroups::class,
            CreateContactGroup::class,
            ListSignatures::class,
            CreateSignature::class,
            ListCorporateStamps::class,
            CreateCorporateStamp::class,
            ManageNotifications::class,
            ManageIntegrations::class,
            ManageSubscription::class,
        ];

        foreach ($pages as $page) {
            Livewire::test($page)->assertSuccessful();
        }
    }

    public function test_record_pages_load_for_seeded_models(): void
    {
        $document = Document::factory()->for($this->user)->create();
        DocumentRecipient::factory()->for($document)->create();
        $contact = Contact::factory()->for($this->user)->create();
        $contactGroup = ContactGroup::factory()->for($this->user)->create();
        $template = Template::factory()->for($this->user)->create();
        $approval = Approval::factory()->for($this->user)->create([
            'status' => ApprovalStatus::Draft,
        ]);
        $contract = Contract::factory()->for($this->user)->create();
        $signature = Signature::factory()->for($this->user)->create([
            'method' => SignatureMethod::Type,
        ]);
        $stamp = CorporateStamp::factory()->for($this->user)->create();

        Livewire::test(ViewDocument::class, ['record' => $document->getRouteKey()])->assertSuccessful();
        Livewire::test(EditDocument::class, ['record' => $document->getRouteKey()])->assertSuccessful();
        Livewire::test(EditContact::class, ['record' => $contact->getRouteKey()])->assertSuccessful();
        Livewire::test(EditContactGroup::class, ['record' => $contactGroup->getRouteKey()])->assertSuccessful();
        Livewire::test(EditTemplate::class, ['record' => $template->getRouteKey()])->assertSuccessful();
        Livewire::test(EditApproval::class, ['record' => $approval->getRouteKey()])->assertSuccessful();
        Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])->assertSuccessful();
        Livewire::test(EditSignature::class, ['record' => $signature->getRouteKey()])->assertSuccessful();
        Livewire::test(EditCorporateStamp::class, ['record' => $stamp->getRouteKey()])->assertSuccessful();
    }

    public function test_list_documents_export_action_does_not_crash(): void
    {
        Livewire::test(ListDocuments::class)
            ->callAction('export')
            ->assertNotified('Export coming soon');
    }

    public function test_document_table_actions_do_not_crash(): void
    {
        $folder = Folder::factory()->for($this->user)->create(['name' => 'Inbox']);
        $document = Document::factory()->for($this->user)->create([
            'status' => DocumentStatus::Pending,
            'file_path' => 'documents/smoke.pdf',
            'original_filename' => 'smoke.pdf',
        ]);
        Storage::disk('local')->put($document->file_path, 'pdf-bytes');
        DocumentRecipient::factory()->for($document)->create([
            'role' => RecipientRole::Signer,
        ]);

        Livewire::test(ListDocuments::class)
            ->callAction(TestAction::make('void')->table($document))
            ->assertNotified('Document voided');

        $this->assertSame(DocumentStatus::Voided, $document->fresh()->status);

        $movable = Document::factory()->for($this->user)->create([
            'status' => DocumentStatus::Draft,
        ]);

        Livewire::test(ListDocuments::class)
            ->callAction(TestAction::make('moveToFolder')->table($movable), [
                'folder_id' => $folder->id,
            ])
            ->assertNotified('Document moved');

        $this->assertSame($folder->id, $movable->fresh()->folder_id);

        $trashable = Document::factory()->for($this->user)->create([
            'status' => DocumentStatus::Draft,
        ]);

        Livewire::test(ListDocuments::class)
            ->callAction(TestAction::make('softDelete')->table($trashable))
            ->assertNotified('Document moved to trash');

        $this->assertSoftDeleted($trashable);
    }

    public function test_document_resend_and_download_actions(): void
    {
        $document = Document::factory()->for($this->user)->create([
            'status' => DocumentStatus::Pending,
            'file_path' => 'documents/download.pdf',
            'original_filename' => 'download.pdf',
        ]);
        Storage::disk('local')->put($document->file_path, 'pdf-bytes');
        DocumentRecipient::factory()->for($document)->create([
            'role' => RecipientRole::Signer,
            'email' => 'signer@example.com',
        ]);

        Livewire::test(ListDocuments::class)
            ->callAction(TestAction::make('resend')->table($document))
            ->assertNotified('Document resent');

        Livewire::test(ListDocuments::class)
            ->callAction(TestAction::make('download')->table($document))
            ->assertSuccessful();
    }

    public function test_emeterai_buy_and_use_actions(): void
    {
        $document = Document::factory()->for($this->user)->create([
            'title' => 'Stamp Me',
        ]);

        Livewire::test(EmeteraiDashboard::class)
            ->callAction('buyEmeterai', data: ['quantity' => 2])
            ->assertNotified();

        $this->assertSame(2, EmeteraiBalance::forUser($this->user)->available);

        Livewire::test(EmeteraiDashboard::class)
            ->callAction('useEmeterai', data: [
                'document_id' => $document->id,
                'document_name' => 'Stamp Me',
            ])
            ->assertNotified('eMeterai stamp applied');

        $balance = EmeteraiBalance::forUser($this->user);
        $this->assertSame(1, $balance->available);
        $this->assertSame(1, $balance->used);
        $this->assertDatabaseHas('emeterai_usages', [
            'user_id' => $this->user->id,
            'document_id' => $document->id,
        ]);
        $this->assertDatabaseHas('emeterai_purchases', [
            'user_id' => $this->user->id,
            'quantity' => 2,
            'status' => EmeteraiPurchaseStatus::Completed->value,
        ]);
    }

    public function test_emeterai_use_with_insufficient_balance_notifies_danger(): void
    {
        Livewire::test(EmeteraiDashboard::class)
            ->callAction('useEmeterai', data: [
                'document_name' => 'No Balance',
            ])
            ->assertNotified('Insufficient eMeterai balance');
    }

    public function test_approval_submit_approve_reject_actions(): void
    {
        $approval = Approval::factory()->for($this->user)->create([
            'title' => 'Smoke Approval',
            'type' => ApprovalType::WithoutDocument,
            'status' => ApprovalStatus::Draft,
        ]);
        ApprovalStep::factory()->for($approval)->create([
            'approver_name' => 'Boss',
            'approver_email' => 'boss@example.com',
            'step_order' => 1,
        ]);

        Livewire::test(EditApproval::class, ['record' => $approval->getRouteKey()])
            ->callAction('submit')
            ->assertNotified('Approval submitted');

        $this->assertSame(ApprovalStatus::Pending, $approval->fresh()->status);

        Livewire::test(EditApproval::class, ['record' => $approval->getRouteKey()])
            ->callAction('approve')
            ->assertNotified('Step approved');

        $this->assertSame(ApprovalStatus::Approved, $approval->fresh()->status);

        $rejected = Approval::factory()->for($this->user)->create([
            'status' => ApprovalStatus::Draft,
        ]);
        ApprovalStep::factory()->for($rejected)->create([
            'step_order' => 1,
        ]);
        $rejected->submit();

        Livewire::test(EditApproval::class, ['record' => $rejected->getRouteKey()])
            ->callAction('reject')
            ->assertNotified('Approval rejected');

        $this->assertSame(ApprovalStatus::Rejected, $rejected->fresh()->status);
    }

    public function test_template_use_action_creates_document_draft(): void
    {
        Storage::disk('local')->put('templates/smoke.pdf', 'pdf');

        $template = Template::factory()->for($this->user)->create([
            'title' => 'NDA Template',
            'file_path' => 'templates/smoke.pdf',
            'original_filename' => 'smoke.pdf',
            'status' => TemplateStatus::Active,
        ]);

        Livewire::test(EditTemplate::class, ['record' => $template->getRouteKey()])
            ->callAction('useTemplate')
            ->assertNotified('Document draft created')
            ->assertRedirect();

        $this->assertDatabaseHas(Document::class, [
            'user_id' => $this->user->id,
            'title' => 'NDA Template',
            'status' => DocumentStatus::Draft->value,
        ]);
    }

    public function test_templates_table_use_action_redirects_to_document_edit(): void
    {
        Storage::disk('local')->put('templates/table-smoke.pdf', 'pdf');

        $template = Template::factory()->for($this->user)->create([
            'title' => 'Table Template',
            'file_path' => 'templates/table-smoke.pdf',
            'original_filename' => 'table-smoke.pdf',
            'status' => TemplateStatus::Active,
        ]);

        Livewire::test(ListTemplates::class)
            ->callAction(TestAction::make('useTemplate')->table($template))
            ->assertNotified('Document draft created')
            ->assertRedirect();

        $document = Document::query()->where('title', 'Table Template')->first();
        $this->assertNotNull($document);
        $this->assertSame(DocumentStatus::Draft, $document->status);
    }

    public function test_create_flows_for_remaining_resources(): void
    {
        Livewire::test(CreateContact::class)
            ->fillForm([
                'name' => 'Smoke Contact',
                'email' => 'smoke.contact@example.com',
                'visibility' => ContactVisibility::Personal->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        Livewire::test(CreateContactGroup::class)
            ->fillForm([
                'name' => 'Smoke Group',
                'description' => 'Group for smoke tests',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        Livewire::test(CreateSignature::class)
            ->fillForm([
                'method' => SignatureMethod::Type->value,
                'fullname' => 'Smoke Signer',
                'initials' => 'SS',
                'is_default' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        Livewire::test(CreateCorporateStamp::class)
            ->fillForm([
                'company_name' => 'Smoke Co',
                'website' => 'https://smoke.test',
                'color' => '#2563eb',
                'is_default' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        Livewire::test(CreateContract::class)
            ->fillForm([
                'title' => 'Smoke Contract',
                'counterparty' => 'Partner',
                'contract_value' => 500000,
                'currency' => 'IDR',
                'status' => ContractStatus::Draft->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $file = UploadedFile::fake()->create('template.pdf', 50, 'application/pdf');

        Livewire::test(CreateTemplate::class)
            ->fillForm([
                'title' => 'Smoke Template',
                'file_path' => $file,
                'sequential_signing' => false,
                'status' => TemplateStatus::Active->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        Livewire::test(CreateApproval::class)
            ->fillForm([
                'title' => 'Smoke Approval Create',
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

        Livewire::test(ManageFolders::class)
            ->callAction('create', data: [
                'name' => 'Smoke Folder',
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas(Folder::class, [
            'user_id' => $this->user->id,
            'name' => 'Smoke Folder',
        ]);
    }

    public function test_create_document_as_draft_via_wizard(): void
    {
        $file = UploadedFile::fake()->create('agreement.pdf', 100, 'application/pdf');

        Livewire::test(CreateDocument::class)
            ->fillForm([
                'document_file' => $file,
                'title' => 'Smoke Wizard Document',
                'recipients' => [
                    [
                        'name' => 'Alice Signer',
                        'email' => 'alice@example.com',
                        'role' => RecipientRole::Signer->value,
                        'signing_order' => 1,
                    ],
                ],
                'fields' => [],
                'email_subject' => 'Please sign',
                'message' => 'Thanks',
                'sequential_signing' => false,
            ])
            ->call('createWithMode', 'draft')
            ->assertHasNoFormErrors();

        $document = Document::query()->where('title', 'Smoke Wizard Document')->first();
        $this->assertNotNull($document);
        $this->assertSame(DocumentStatus::Draft, $document->status);
        $this->assertCount(1, $document->recipients);
    }

    public function test_api_documents_endpoints(): void
    {
        $document = Document::factory()->for($this->user)->create([
            'title' => 'API Doc',
        ]);

        auth()->logout();

        $this->getJson('/api/documents')->assertUnauthorized();

        $token = $this->user->createToken('smoke')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/documents')
            ->assertOk()
            ->assertJsonFragment(['title' => 'API Doc']);

        $this->withToken($token)
            ->getJson("/api/documents/{$document->id}")
            ->assertOk()
            ->assertJsonFragment(['title' => 'API Doc']);
    }

    public function test_public_signing_pages(): void
    {
        $document = Document::factory()->for($this->user)->create([
            'status' => DocumentStatus::Pending,
            'title' => 'Public Sign',
        ]);
        $recipient = DocumentRecipient::factory()->for($document)->create([
            'role' => RecipientRole::Signer,
            'name' => 'Public Signer',
        ]);

        $this->get(route('signing.show', $recipient->access_token))
            ->assertOk()
            ->assertSee('Public Sign');

        $this->post(route('signing.sign', $recipient->access_token), [
            'signature' => 'Public Signer',
        ])->assertRedirect(route('signing.show', $recipient->access_token));

        $this->assertSame(DocumentStatus::Completed, $document->fresh()->status);
    }

    public function test_signature_create_page_with_each_method_visibility(): void
    {
        foreach ([SignatureMethod::Type, SignatureMethod::Draw, SignatureMethod::Upload] as $method) {
            Livewire::test(CreateSignature::class)
                ->fillForm(['method' => $method->value])
                ->assertSuccessful()
                ->assertHasNoFormErrors(['method']);
        }
    }
}
