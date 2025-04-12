<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SavedTimesMail extends Mailable
{
    use SerializesModels;

    public $pdfContent;
    public $recipientEmail;

    public function __construct($pdfContent, $recipientEmail)
    {
        $this->pdfContent = $pdfContent;
        $this->recipientEmail = $recipientEmail;
    }

    public function build()
    {
        return $this->subject('Your Saved Times PDF')
                    ->to($this->recipientEmail)
                    ->attachData($this->pdfContent, 'saved_times.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->view('emails.saved_times');
    }
}


class SavedTimesMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Saved Times Mail',
            from: new Address ('notchapplejb@gmail.com', 'The Clock App Team ')
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
