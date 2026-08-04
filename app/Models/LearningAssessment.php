<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningAssessment extends Model
{
    protected $fillable = [
        'lesson_id', 'question', 'options', 'correct_option', 'explanation', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['options' => 'array'];
    }
}
