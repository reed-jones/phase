<?php

namespace Phased\Tests\State\Models;

use Phased\State\Models\Model;

class ExtendedUser extends Model
{
    protected $table = 'users';

    protected $guarded = [];
}
