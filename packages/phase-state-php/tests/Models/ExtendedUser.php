<?php

namespace Phased\State\Test\Models;

use Phased\State\Models\Model;

class ExtendedUser extends Model
{
    protected $table = 'users';

    protected $guarded = [];
}
