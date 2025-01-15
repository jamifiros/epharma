<?php

namespace App\Mail;

use App\Models\Medicine;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuardianAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $medicine;

    /**
     * Create a new message instance.
     *
     * @param Medicine $medicine
     */
    public function __construct(Medicine $medicine)
    {
        $this->medicine = $medicine;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Low Stock Alert: Medicine')
            ->view('emails.guardian-alert')
            ->with('medicine', $this->medicine);
    }
}
