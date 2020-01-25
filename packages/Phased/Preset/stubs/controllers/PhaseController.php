<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Phased\Routing\Facades\Phase;
use Phased\State\Facades\Vuex;

class PhaseController
{
    /**
     * This is the Phase demo home page. The controller
     * name will match vue component found at
     *  - resources/js/pages/{Controller}/{Method}
     * In this case it will be
     *  - resources/js/pages/PhaseController/HomePage.vue.
     */
    public function HomePage()
    {
        // Store the 'phase' into $store.state.app.name
        Vuex::module('app', [
            'name' => 'Phase',
        ]);

        // returning Phase::view() for SPA routes, will automatically
        // switch between loading the full page for direct entry, or just
        // the updated vuex data for SPA navigation/api calls
        return Phase::view();
    }

    public function AboutPage()
    {
        return Phase::view();
    }
}
