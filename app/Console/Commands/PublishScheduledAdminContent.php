<?php

namespace App\Console\Commands;

use App\Models\AdminAuditLog;
use App\Models\DailyGamePlan;
use App\Models\LearningCourse;
use App\Models\LiveSession;
use App\Models\MarketReport;
use Illuminate\Console\Command;

class PublishScheduledAdminContent extends Command
{
    protected $signature = 'admin:publish-scheduled';
    protected $description = 'Publish due OHC admin content';

    public function handle(): int
    {
        $count = 0;
        foreach ([DailyGamePlan::class, LiveSession::class, MarketReport::class, LearningCourse::class] as $model) {
            $model::query()->where('status', 'scheduled')->where('scheduled_for', '<=', now())
                ->chunkById(100, function ($items) use (&$count) {
                    foreach ($items as $item) {
                        $item->forceFill(['status' => 'published', 'scheduled_for' => null, 'published_at' => now()])->save();
                        AdminAuditLog::create([
                            'action' => 'content.auto_published',
                            'subject_type' => $item::class,
                            'subject_id' => (string) $item->getKey(),
                            'summary' => 'Published scheduled content: '.($item->title ?? $item->name ?? $item->getKey()),
                        ]);
                        $count++;
                    }
                });
        }
        $this->info("Published {$count} scheduled item(s).");
        return self::SUCCESS;
    }
}
