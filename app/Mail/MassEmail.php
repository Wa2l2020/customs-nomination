<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MassEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailContent;
    public $emailSubject;

    public function __construct($subject, $content)
    {
        $this->emailSubject = $subject;
        $this->emailContent = $content;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mass_email',
        );
    }
}
