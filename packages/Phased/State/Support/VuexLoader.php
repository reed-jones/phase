<?php

namespace Phased\State\Support;

use Illuminate\Support\Str;
use ReflectionClass;

class VuexLoader
{
    /**
     * Retrieves or guesses the desired namespace for this vuex module.
     *
     * @return string
     */
    public function getNamespace()
    {
        if (isset($this->namespace)) {
            return $this->namespace;
        }

        // default to lowercased class name (without ModuleLoader)
        return str_replace(
            'ModuleLoader',
            '',
            Str::camel((new ReflectionClass($this))->getShortName())
        );
    }
}
