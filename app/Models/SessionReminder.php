<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionReminder extends Model
{
    protected $fillable = ['user_id', 'live_session_id', 'remind_at'];
    protected function casts(): array { return ['remind_at' => 'datetime']; }
}
