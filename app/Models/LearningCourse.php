<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningCourse extends Model
{
    protected $fillable = [
        'key', 'title', 'category', 'description', 'status', 'is_free',
        'sort_order', 'scheduled_for', 'published_at',
    ];

    protected function casts(): array
    {
        return ['is_free' => 'boolean', 'scheduled_for' => 'datetime', 'published_at' => 'datetime'];
    }

    public function modules()
    {
        return $this->hasMany(LearningModule::class, 'course_id')->orderBy('sort_order');
    }
}
