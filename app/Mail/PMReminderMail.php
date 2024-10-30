<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PMReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asset;

    public function __construct($asset)
    {
        $this->asset = $asset;
    }

    public function build()
    {
        return $this->subject('Preventive Maintenance Reminder')
                    ->view('emails.pm_reminder');
    }
}
