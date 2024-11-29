<?php

namespace App\Mail;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class StationCreatedMail extends Mailable
{
    use Queueable, SerializesModels;


    /**
     * Create a new message instance.
     */
    public function __construct(public User $user)
    {
        //
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Station Created Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $token = uuid_create();
        PasswordResetToken::create(['email' => $this->user->email,'token' => $token, 'created_at' => now()]);
        $activationLink = URL::route('accounts.verify', ['id' => $this->user->id, 'hash' => $token ]);
        return new Content(
            view: 'mails.station_manager_active_account_email',
            with: ['user' => $this->user, 'token' => $token, 'activationLink' => $activationLink]
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
