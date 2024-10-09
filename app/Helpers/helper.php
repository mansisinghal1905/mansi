<?php
use App\Mail\PurposalSend;
use Illuminate\Support\Facades\Mail;

function sendMail($email, $template, $data)
{
    try {
        Mail::to($email)->send(new PurposalSend($data));
        info("Mail sent successfully to $email");
    } catch (\Exception $e) {
        info("Failed to send mail: " . $e->getMessage());
    }
}


if (!function_exists('actions')) {
    function actions($data)
    {
           $action = '<div class="hstack gap-2 justify-content-end">';
        if(isset($data['edit'])){
            $action.='<a href="'.$data['edit'].'" class="avatar-text avatar-md">
                    <i class="feather feather-edit"></i>
                </a>';
        }

        if(isset($data['view']) || isset($data['delete']))
        {
            $action.='<div class="dropdown">
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0)">
                            <i class="feather feather-view me-3"></i>
                            <span>Vies</span>
                        </a>
                    </li>
                </ul>
            </div>';
    }
$action.='</div>';
return $action;
    }
}

function generateInvoiceNumber($invoice_id) {
    // Prefix for the invoice number
    $prefix = 'INV-';
    
    // Generate a random 6-digit number
    $randomNumber = mt_rand(100000, 999999);
    
    // Concatenate the prefix, random number, and invoice_id
    $invoiceNumber = $prefix . $randomNumber . $invoice_id;
    
    return $invoiceNumber;
}


// if (!function_exists('public_path')) {
//     function public_path()
//     {
//         $public_path = env('APP_URL').'public';
//         return $public_path;
//     }
// }
