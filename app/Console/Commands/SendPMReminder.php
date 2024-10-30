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
            $query->where(function ($subQuery) {
                    // 35% threshold for critical assets
                    $subQuery->where('classification', 'Critical')
                        ->whereRaw('(std_stroke - current_qty) / std_stroke <= 0.35')
                        ->whereColumn('current_qty', '<', 'std_stroke');
                })
                ->orWhere(function ($subQuery) {
                    // 20% threshold for non-critical assets or assets without a classification
                    $subQuery->where('classification', '!=', 'Critical')
                        ->orWhereNull('classification')
                        ->whereRaw('(std_stroke - current_qty) / std_stroke <= 0.2')
                        ->whereColumn('current_qty', '<', 'std_stroke');
                });
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
                'std_stroke' => $asset->std_stroke,
                'current_qty' => $asset->current_qty,
                'pmLink' => $pmLink
            ];

            try {
                Mail::send('emails.pm_reminder', $emailData, function ($message) use ($asset) {
                    $message->to('muhammad.taufik@ptmkm.co.id')
                            ->subject("PM Reminder for Asset: {$asset->asset_no}");
                });

                $this->info("Sent PM reminder for Asset No: {$asset->asset_no}");
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
