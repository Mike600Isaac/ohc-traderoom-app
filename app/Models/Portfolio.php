<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $fillable = ['user_id', 'name', 'benchmark_symbol', 'currency'];
    public function holdings() { return $this->hasMany(Holding::class); }
}
