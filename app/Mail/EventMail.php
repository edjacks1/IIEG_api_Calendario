<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details=$details;
    }

    public function build()
    {
        $subject="Información de tu Evento";
        
        if($this->details['notification_type']=='create')
            $subject="Has sido invitado al Evento" . $this->details['title'];
        else if($this->details['notification_type']=='update')
            $subject="El Evento ". $this->details['title']." ha sido actualizado";
        else if($this->details['notification_type']=='checklist')
            $subject="El Evento ". $this->details['title']." esta proximo a iniciar";

        return $this->subject($subject)
                    ->view('mail.event');
    }
}
