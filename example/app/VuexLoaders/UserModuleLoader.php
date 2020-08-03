<?php

namespace App\VuexLoaders;

use Illuminate\Support\Facades\Auth;
use Phased\State\Support\VuexLoader;

class UserModuleLoader extends VuexLoader
{
    public function profile() {
        return Auth::user();
    }

    public function counter($number) {
        return $number;
    }
}
