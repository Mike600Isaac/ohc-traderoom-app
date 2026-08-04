<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityChannel extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'required_path', 'status'];
    public function posts() { return $this->hasMany(CommunityPost::class, 'channel_id'); }
}
