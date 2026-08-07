<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class PublishingService
{
    public function apply(Model $model, string $status, mixed $scheduledFor = null): void
    {
        $model->status = $status;
        $model->scheduled_for = $status === 'scheduled' ? $scheduledFor : null;
        $model->published_at = $status === 'published' ? ($model->published_at ?: now()) : null;
    }
}
