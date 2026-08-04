<?php

namespace App\Services;

use App\Models\AnalyticsEvent;
use App\Models\User;

class AnalyticsRecorder
{
    public function record(?User $user, string $event, ?string $subjectType = null, string|int|null $subjectId = null, array $metadata = []): void
    {
        AnalyticsEvent::create([
            'user_id' => $user?->id,
            'event_name' => $event,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId === null ? null : (string) $subjectId,
            'metadata' => $metadata ?: null,
            'occurred_at' => now(),
        ]);
    }
}
