<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    protected $table = 'course_progress';

    protected $fillable = [
        'user_id', 'course_key', 'current_item_title', 'completed_items',
        'total_items', 'resume_url', 'last_activity_at',
    ];

    protected function casts(): array
    {
        return ['last_activity_at' => 'datetime'];
    }

    public function getPercentageAttribute(): ?int
    {
        if (! $this->total_items || $this->total_items < 1) {
            return null;
        }

        return min(100, (int) round(($this->completed_items / $this->total_items) * 100));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}