<?php

namespace Phased\State\Facades;

use Illuminate\Support\Facades\Facade;
use Phased\State\Factories\VuexFactory;

class Vuex extends Facade {
    protected static function getFacadeAccessor()
    {
        return VuexFactory::class;
    }
}
