<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeletedNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $group;
    public $text;
    public $user;
    public $url;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($group, $text, $user = 'deleted account', $url = '')
    {
        $this->group = $group;
        $this->text = $text;
        $this->user = $user;
        $this->url = env('APP_URL') . '/' . $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.deleted-from-all-groups')
            ->subject($this->group . ': ' . $this->text);
    }
}
