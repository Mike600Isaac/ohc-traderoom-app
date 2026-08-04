<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = ['user_id', 'type', 'in_app', 'email', 'push', 'quiet_start', 'quiet_end'];
    protected function casts(): array { return ['in_app' => 'boolean', 'email' => 'boolean', 'push' => 'boolean']; }
}
