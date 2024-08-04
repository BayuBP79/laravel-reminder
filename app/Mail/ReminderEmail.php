<?php

namespace App\Mail;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $reminder;

    /**
     * Create a new message instance.
     */
    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
    }

    public function build()
    {
        return $this->view('emails.reminder')
                    ->subject('Reminder Email');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder Email',
        );
    }

    public function content(): Content
    {
        return (new Content('emails.reminder'))->with([
            'reminder' => $this->reminder,
        ]);

    }

    public function attachments(): array
    {
        return [];
    }
}
