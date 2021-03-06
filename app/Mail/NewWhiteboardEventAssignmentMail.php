<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewWhiteboardEventAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $group;
    public $what;
    public $the_new_thing;
    public $url;
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($group, $what, $the_new_thing = null)
    {
        $this->group = $group;
        $this->what = $what;
        $this->the_new_thing = $the_new_thing;
        if($what == 'whiteboard posts'){
            $this->url = env('APP_URL') . '/groups/' . $group->id . '/whiteboard';
        }
        else{
            $this->url = env('APP_URL') . '/' .  $what . '/' . $the_new_thing->id;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.notify-about-new-thing')
            ->subject($this->group->name . ': there is a new ' . substr($this->what, 0, -1));
    }
}
