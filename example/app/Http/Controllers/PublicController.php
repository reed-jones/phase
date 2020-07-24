<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Phased\Routing\Facades\Phase;
use Phased\State\Facades\Vuex;

class PublicController extends Controller
{
    public function HomePage()
    {
        return Phase::view();
    }
}
