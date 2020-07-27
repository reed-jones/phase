<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Phased\Routing\Facades\Phase;
use Phased\State\Facades\Vuex;

class PublicController extends Controller
{
    public function HomePage()
    {
        collect(Notice::query()
            ->when(Auth::check(),
                fn ($query) => $query->with('user:id,name,email,phone'),
                fn ($query) => $query->with('user:id,name')
            )
            ->paginate(request()->input('per_page', 10)))
            ->toVuex('notices', 'all');

        return Phase::view();
    }
}
