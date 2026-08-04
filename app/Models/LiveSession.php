<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveSession extends Model
{
    protected $fillable = ['host_user_id', 'title', 'agenda', 'recap', 'starts_at', 'ends_at', 'join_url', 'replay_url', 'status', 'scheduled_for', 'registered_count', 'published_at'];
    protected function casts(): array { return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'scheduled_for' => 'datetime', 'published_at' => 'datetime']; }
    public function host() { return $this->belongsTo(User::class, 'host_user_id'); }
}
