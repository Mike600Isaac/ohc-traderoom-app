<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    protected $table = 'learning_lesson_progress';
    protected $fillable = ['user_id', 'lesson_id', 'notes', 'bookmarked', 'last_viewed_at', 'completed_at'];
    protected function casts(): array { return ['bookmarked' => 'boolean', 'last_viewed_at' => 'datetime', 'completed_at' => 'datetime']; }
}
