<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Medicine;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLowStockEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $lowStockMedicines;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @param  array  $lowStockMedicines
     * @return void
     */
    public function __construct(User $user, array $lowStockMedicines)
    {
        $this->user = $user;
        $this->lowStockMedicines = $lowStockMedicines;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Prepare the email content
        $emailData = [
            'user_name' => $this->user->name,
            'low_stock_medicines' => $this->lowStockMedicines,
        ];

        // Get the user's guardian email
        $guardianMail = $this->user->userDetails->guardian_mail ?? null;

        if ($guardianMail) {
            Mail::send('emails.low_stock_alert', $emailData, function ($message) use ($guardianMail) {
                $message->to($guardianMail)
                        ->subject('Low Stock Alert for Medicines');
            });
        }
    }
}
