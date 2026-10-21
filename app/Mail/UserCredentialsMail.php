<?php

namespace App\Mail;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $plainPassword,
        public Organization $organization,
        public ?Role $role = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $orgName = $this->organization->name ?? config('app.name');

        return new Envelope(
            subject: 'Your Account Credentials - '.$orgName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $roleName = $this->role?->name ?? $this->user->role?->name ?? 'Staff';

        return new Content(
            markdown: 'emails.user-credentials',
            with: [
                'user' => $this->user,
                'plainPassword' => $this->plainPassword,
                'organization' => $this->organization,
                'roleName' => $roleName,
                'loginUrl' => route('login'),
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
