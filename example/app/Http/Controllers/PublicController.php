<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Phased\Routing\Facades\Phase;

class PublicController extends Controller
{
    public function HomePage()
    {
        return Phase::view();
    }
}
