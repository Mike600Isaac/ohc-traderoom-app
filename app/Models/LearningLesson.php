<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningLesson extends Model
{
    protected $fillable = [
        'module_id', 'title', 'body', 'video_url', 'document_path',
        'duration_minutes', 'is_preview', 'status', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_preview' => 'boolean'];
    }

    public function module()
    {
        return $this->belongsTo(LearningModule::class, 'module_id');
    }

    public function assessments()
    {
        return $this->hasMany(LearningAssessment::class, 'lesson_id')->orderBy('sort_order');
    }
}
