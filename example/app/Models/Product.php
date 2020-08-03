<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    public function user() {
        return $this->belongsTo(User::class);
    }
}
