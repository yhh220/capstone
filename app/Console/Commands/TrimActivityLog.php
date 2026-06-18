<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class TrimActivityLog extends Command
{
    protected $signature = 'activitylog:trim {--keep=5000 : How many of the most recent activities to keep}';

    protected $description = 'Cap the activity log — keep only the most recent N records and delete the older ones.';

    public function handle(): int
    {
        $keep = max(0, (int) $this->option('keep'));

        // Id of the (keep+1)-th most recent row; everything at or below it is old.
        $cutoffId = Activity::query()
            ->orderByDesc('id')
            ->skip($keep)
            ->take(1)
            ->value('id');

        if ($cutoffId === null) {
            $this->info("Activity log is within the {$keep}-record limit — nothing to trim.");

            return self::SUCCESS;
        }

        $deleted = Activity::query()->where('id', '<=', $cutoffId)->delete();

        $this->info("Trimmed {$deleted} old activity record(s); kept the most recent {$keep}.");

        return self::SUCCESS;
    }
}
