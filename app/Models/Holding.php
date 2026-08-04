<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holding extends Model
{
    protected $fillable = ['portfolio_id', 'symbol', 'name', 'asset_class', 'quantity', 'average_cost', 'target_weight'];
    protected function casts(): array { return ['quantity' => 'decimal:8', 'average_cost' => 'decimal:8', 'target_weight' => 'decimal:2']; }
    public function portfolio() { return $this->belongsTo(Portfolio::class); }
}
