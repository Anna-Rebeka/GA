<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invite;

class InviteMemberMail extends Mailable
{
    use Queueable, SerializesModels;

    public $group;
    public $user;
    public $invite;
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($group, $user, Invite $invite)
    {
        $this->group = $group;
        $this->user = $user;
        $this->invite = $invite;
        $this->url = env('APP_URL') . '/invite-member/' . $invite->token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.invite-member-mail')
            ->subject('A group invitation from Radar');
    }
}
