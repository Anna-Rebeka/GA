<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FromAllGroupsNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $group;
    public $text;
    public $url;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($group, $text, $url)
    {
        $this->group = $group;
        $this->text = $text;
        $this->url = url()->current() . $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.notify-from-all-groups')
            ->subject($this->group . ': ' . $this->text);
    }
}
