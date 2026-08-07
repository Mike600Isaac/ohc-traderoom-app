<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = ['user_id', 'instrument', 'direction', 'entry_price', 'stop_price', 'target_price', 'risk_percent', 'emotion', 'confidence', 'outcome', 'r_multiple', 'screenshot_path', 'lessons', 'traded_at'];

    protected function casts(): array
    {
        return ['entry_price' => 'decimal:8', 'stop_price' => 'decimal:8', 'target_price' => 'decimal:8', 'risk_percent' => 'decimal:2', 'r_multiple' => 'decimal:2', 'traded_at' => 'datetime'];
    }
}
