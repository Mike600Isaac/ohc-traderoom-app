<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyGamePlan extends Model
{
    protected $fillable = [
        'author_user_id', 'trading_date', 'title', 'status', 'scheduled_for',
        'market', 'bias', 'key_levels', 'invalidation', 'watchlist', 'video_url',
        'pdf_url', 'chart_url', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'trading_date' => 'date',
            'scheduled_for' => 'datetime',
            'key_levels' => 'array',
            'watchlist' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }
}
