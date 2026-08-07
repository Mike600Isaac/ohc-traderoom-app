<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyGoal extends Model
{
    protected $fillable = ['user_id', 'week_starts_on', 'target', 'completed'];

    protected function casts(): array
    {
        return ['week_starts_on' => 'date'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}