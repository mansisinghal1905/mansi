<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class UserDetailsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $developer;
    public $password;
    /**
     * Create a new message instance.
     */
    public function __construct($developer, $password)
    {
        $this->user = $developer;
        $this->password = $password;
    }

    public function build()
    {
        return $this->view('emails.user_email')
                    ->subject('Your Account Details')
                    ->with([
                        'name' => $this->user->name,
                        'email' => $this->user->email,
                        'password' => $this->password,
                    ]);
    }
    
}
