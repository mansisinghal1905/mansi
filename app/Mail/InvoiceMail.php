<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade as PDF;


class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $invoice;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        $pdf = PDF::loadView('admin.invoice_payments.view', ['invoice' => $this->invoice]);

        return $this->view('emails.invoice')
                    ->attachData($pdf->output(), 'invoice-' . $this->invoice->id . '.pdf')
                    ->subject('Your Invoice');
    }
}

