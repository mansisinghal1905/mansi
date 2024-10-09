<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HostingCustomer; // Your Subscription model
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionReminder;
use Illuminate\Support\Facades\Log;


class SendSubscriptionReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send subscription renewal reminder 10 days before the end date';

    /**
     * Execute the console command.
     */
    
     public function handle()
     {
         // Get the date that is 10 days from now
         $currectDate = Carbon::now();

        //  Log::info('Reminder date calculated: ' . $reminderDate);
            
         // Define an array of subscription types to check
         $subscriptionTypes = ['monthly', 'semi-annual', 'annual'];
     
         // Loop through each subscription type
         foreach ($subscriptionTypes as $type) {
            // Get all subscriptions for a specific type
            $subscriptions = HostingCustomer::where('subscription', $type)
                ->get();
    
            foreach ($subscriptions as $subscription) {
                // Assume the end date is stored in the subscription model
                $endDate = Carbon::parse($subscription->end_date);
                // Log::info('Reminder date calculated: ' . $endDate);
                // Check if the subscription end date is 10 days from today
                if ($endDate->isSameDay($currectDate)) {
                    // Send reminder email
                    Mail::to($subscription->email)->send(new SubscriptionReminder($subscription));
    
                    // Output the subscription type and user for debugging
                    $this->info("Reminder sent to {$subscription->email} for a {$type} subscription.");
                }
            }
        }
     
         $this->info('Subscription reminders sent for subscriptions ending in 10 days.');
     }
     

}
