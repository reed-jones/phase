<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'content'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
