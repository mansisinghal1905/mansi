<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $ticket_code;
    public $type;
    public $attachment;
    

    /**
     * Create a new message instance.
     */
    public function __construct($ticket, $ticketcode,$types, $attachments = [])
    {
        // dd($types);
        // dd($type1);
        $this->user = $ticket;
        $this->ticket_code = $ticketcode;
        $this->attachment = is_array($attachments) ? $attachments : [];
        $this->type = $types;
        //  dd($this->type);
    }

    public function build()
    {
        $cname =$this->type;
       
        // if($this->user->getAllHostCustomer->name){
        //     $cname .=$this->user->getAllHostCustomer->name ." ";
        // }
        

        $email = $this->view('emails.ticket_email')
                      ->subject('New Ticket Created')
                      ->with([
                          'customer_name' => $cname,
                          'subject' => $this->user->subject,
                          'priority' => $this->user->priority,
                          'ticket_code' => $this->ticket_code,
                      ]);

        // Attach files if there are any
        if (!empty($this->attachment)) {
            foreach ($this->attachment as $filePath) {
                $email->attach($filePath);
            }
        }

        return $email;
    }
}
