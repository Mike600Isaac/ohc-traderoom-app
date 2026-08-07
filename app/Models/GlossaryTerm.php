<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlossaryTerm extends Model
{
    protected $fillable = ['term', 'definition', 'status', 'author_user_id'];
}
