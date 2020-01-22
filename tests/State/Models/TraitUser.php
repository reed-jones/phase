<?php

namespace Phased\Tests\State\Models;

use Illuminate\Database\Eloquent\Model;
use Phased\State\Traits\Vuexable;

class TraitUser extends Model
{
    use Vuexable;

    protected $table = 'users';

    protected $guarded = [];
}
