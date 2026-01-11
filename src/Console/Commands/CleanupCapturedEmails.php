<?php

namespace Ghijk\EmailPreview\Console\Commands;

use Ghijk\EmailPreview\Models\CapturedEmail;
use Illuminate\Console\Command;

class CleanupCapturedEmails extends Command
{
    protected $signature = 'email-preview:cleanup {--days= : Number of days to retain emails}';

    protected $description = 'Delete captured emails older than the specified number of days';

    public function handle(): int
    {
        $days = $this->option('days') ?? config('email-preview.retention_days', 7);

        $this->info("Cleaning up captured emails older than {$days} days...");

        $cutoffDate = now()->subDays($days);

        $count = CapturedEmail::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->comment('No emails to clean up.');

            return self::SUCCESS;
        }

        if (! $this->confirm("Delete {$count} emails?", true)) {
            $this->comment('Cleanup cancelled.');

            return self::SUCCESS;
        }

        CapturedEmail::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Deleted {$count} captured emails.");

        return self::SUCCESS;
    }
}
