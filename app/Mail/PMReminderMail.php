<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PMReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asset;
    public $pmLink;

    public function __construct($asset, $pmLink)
    {
        $this->asset = $asset;
        $this->pmLink = $pmLink;
    }

    public function build()
    {
        return $this->subject('Preventive Maintenance Reminder')
                    ->view('emails.pm_reminder');
    }
}

