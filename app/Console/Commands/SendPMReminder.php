<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MstStrokeDies;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendPMReminder extends Command
{
    // The name and signature of the console command
    protected $signature = 'pm:send-reminder';

    // The console command description
    protected $description = 'Send a preventive maintenance reminder for assets nearing their standard stroke';

    // Execute the console command
    public function handle()
    {
        $assets = MstStrokeDies::where(function ($query) {
            $query->whereRaw('current_qty > reminder_stroke');
        })->get();

        // Loop through assets and send reminder emails
        foreach ($assets as $asset) {
            // Generate the PM link with encrypted ID
            $pmLink = url('/dies/pm/' . encrypt($asset->id));

            // Prepare email data
            $emailData = [
                'asset_no' => $asset->asset_no,
                'part_name' => $asset->part_name,
                'code' => $asset->code,
                'process' => $asset->process,
                'std_stroke' => $asset->std_stroke,
                'current_qty' => $asset->current_qty,
                'pmLink' => $pmLink
            ];

            try {
                Mail::send('emails.pm_reminder', $emailData, function ($message) use ($asset) {
                    $message->to('prasetyo@ptmkm.co.id')
                            ->subject("PM Reminder for Dies: {$asset->code} - {$asset->process}");
                });

                $this->info("PM Reminder for Dies: {$asset->code} - {$asset->process}");
            } catch (\Exception $e) {
                // Log the error message
                \Log::error("Failed to send PM reminder for Asset No: {$asset->asset_no}. Error: " . $e->getMessage());
                $this->error("Failed to send PM reminder for Asset No: {$asset->asset_no}");
            }


            // Output for each email sent
            $this->info("Sent PM reminder for Asset No: {$asset->asset_no}");
        }

        $this->info('PM reminder emails sent successfully!');
        return Command::SUCCESS;
    }
}
