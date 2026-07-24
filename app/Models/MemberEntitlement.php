<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberEntitlement extends Model
{
    protected $fillable = [
        'user_id',
        'external_reference',
        'offer_name',
        'product_name', 
        'offer_type',
        'status',
        'started_at',
        'expires_at',
    ];

    /**
     * Link back to the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
