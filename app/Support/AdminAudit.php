<?php

namespace App\Support;

use App\Models\AdminAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AdminAudit
{
    public static function record(
        Request $request,
        string $action,
        Model|string $subject,
        string $summary,
        array $changes = []
    ): void {
        AdminAuditLog::create([
            'actor_user_id' => $request->user()?->id,
            'action' => $action,
            'subject_type' => $subject instanceof Model ? $subject::class : $subject,
            'subject_id' => $subject instanceof Model ? (string) $subject->getKey() : null,
            'summary' => $summary,
            'changes' => $changes ?: null,
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
        ]);
    }

    public static function changes(Model $model): array
    {
        return collect($model->getChanges())
            ->except(['password', 'remember_token', 'updated_at'])
            ->map(fn ($value, $key) => [
                'from' => $model->getOriginal($key),
                'to' => $value,
            ])->all();
    }
}
