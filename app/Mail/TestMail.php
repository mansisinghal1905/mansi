<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public function __construct($data)
    {
        $this->data = $data;

    }

    public function build()
    {
        return $this->view('emails.clientmail') // Replace with your view
                    ->with('data', $this->data);
                    if(isset($this->data['link'])){
                        $email = $email->attach($this->data['link']);
                    }
                    return $email;
    }
    
   
}
