<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SendPurposal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request;

class TestCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    // public function handle()
    // {
    // try {
    //     $sendpurposal = SendPurposal::get(); 
    //     if($sendpurposal){

    //         foreach($sendpurposal as $k => $value){

    //             if(Carbon::parse($value->schedule_date)->isSameDay(Carbon::now())){
                    
    //                  $getClient =  User::where("id",$value->client_id)->first();
                    
    //                     if(isset($getClient->email)){
    //                         info("document".$value->document);
    //                         info("Send mail to send mail: " . asset('purposaldocument/'.$value->document));
    //     //                             $data = ['schedule_date' => $value->schedule_date,"link"=>public_path('purposaldocument/'$value->document)];
    //     // sendMail('mansisinghal1905@gmail.com', 'abc', $data);
 
    //                     }
    //                 }
    //         }
    //     }
    //     // $data = ['name' => '05-09-2024'];
    //     // sendMail('mansisinghal1905@gmail.com', 'abc', $data);
    // } catch (\Exception $e) {
    //     info("Failed to send mail: " . $e->getMessage());
    // }

    //     }

    public function handle()
    {
        try {
            info("Document: " . 'sdf');
            $sendpurposal = SendPurposal::where("mail_status",0)->get(); 
            if ($sendpurposal) {
               
                foreach ($sendpurposal as $k => $value) {
                    // Check if the schedule date is the same as today
                    info("sendefdsds: " . $value->schedule_date);

                    if (Carbon::parse($value->schedule_date)->isSameDay(Carbon::now())) {
                        info("send: " . $value->schedule_date);
                        $getClient = User::where("id", $value->client_id)->first();
                        $client = $value->getClient;
                        
                        if (isset($getClient->email)) {
                            // Log document information
                            info("Document: " . $value->document);
                            
                            // Correct the path to the document
                            $documentPath = asset('/public/purposaldocument/' . $value->document);
                            info("Send mail with link: " . $documentPath);
                            
                            // Prepare email data
                            $data = [
                                'schedule_date' => $value->schedule_date,
                                'link' => $documentPath,
                                'name' => $client->name
                            ];
                            info("Send Client mail : " . $getClient->email);

                            
                            // Send mail (uncomment this line to actually send the mail)
                            sendMail($getClient->email, 'abc', $data);
                            $value ->mail_status = 1;
                            $value->save();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            info("Failed to send mail: " . $e->getMessage());
        }
    }

}
