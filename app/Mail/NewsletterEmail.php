<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $bodyContent;

    /**
     * Create a new message instance.
     */
    public function __construct(string $content)
    {
        $lines = explode("\n", trim($content));
        if (str_starts_with(trim($lines[0]), 'Subject:')) {
            $this->emailSubject = trim(str_replace('Subject:', '', $lines[0]));
            array_shift($lines);
        } else {
            $this->emailSubject = 'SCRYPT Institutional Insights';
        }
        $this->bodyContent = trim(implode("\n", $lines));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.newsletter',
            with: [
                'bodyContent' => $this->bodyContent
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
