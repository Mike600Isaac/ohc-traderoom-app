<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    protected $fillable = ['channel_id', 'user_id', 'parent_id', 'body', 'attachment_url'];
    public function author() { return $this->belongsTo(User::class, 'user_id'); }
    public function channel() { return $this->belongsTo(CommunityChannel::class, 'channel_id'); }
    public function replies() { return $this->hasMany(self::class, 'parent_id')->with('author')->oldest(); }
}
