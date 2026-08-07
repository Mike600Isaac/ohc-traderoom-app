<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketReport extends Model
{
    protected $fillable = [
        'author_user_id', 'title', 'slug', 'category', 'summary', 'body',
        'status', 'scheduled_for', 'published_at',
    ];

    protected function casts(): array
    {
        return ['scheduled_for' => 'datetime', 'published_at' => 'datetime'];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }
}
