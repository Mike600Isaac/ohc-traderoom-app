<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    public const CREATED_AT = 'occurred_at';
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'event_name', 'subject_type', 'subject_id', 'metadata', 'occurred_at'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'occurred_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
