<?php

namespace App\Notifications;

use App\Models\Document;
use App\Models\DocumentRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentSigningInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Document $document,
        public DocumentRecipient $recipient,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('signing.show', $this->recipient->access_token);

        return (new MailMessage)
            ->subject($this->document->email_subject ?: "Please sign: {$this->document->title}")
            ->greeting("Hello {$this->recipient->name},")
            ->line($this->document->message ?: "You have been invited to sign \"{$this->document->title}\".")
            ->action('Review & sign', $url)
            ->line('If you were not expecting this document, you can ignore this email.');
    }
}
