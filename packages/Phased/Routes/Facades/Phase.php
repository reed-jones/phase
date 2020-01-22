<?php

namespace Phased\Routing\Facades;

use Illuminate\Support\Facades\Facade;
use Phased\Routing\Factories\PhaseFactory;

class Phase extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return PhaseFactory::class;
    }
}
