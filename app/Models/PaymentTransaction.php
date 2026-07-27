<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'member_entitlement_id',
        'course_id',
        'course_name',
        'amount',
        'currency',
        'reference',
        'access_code',
        'authorization_url',
        'status',
        'gateway_response',
        'paystack_transaction_id',
        'metadata',
        'verified_payload',
        'paid_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'verified_payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entitlement()
    {
        return $this->belongsTo(MemberEntitlement::class, 'member_entitlement_id');
    }
}
