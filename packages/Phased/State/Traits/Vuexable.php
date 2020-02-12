<?php

declare(strict_types=1);

namespace Phased\State\Traits;

use Phased\State\Exceptions\VuexInvalidKeyException;
use Phased\State\Facades\Vuex;

trait Vuexable
{
    /**
     * Formats the current model & stores to local vuex state.
     *
     * @param string|null [$namespace=null]
     * @param string|null [$key=null]
     *
     * @return $this
     */
    public function toVuex($namespace, $key = null)
    {
        // if only one key is provided, it will get saved
        // to the base state (non-module)
        if (! $key) {
            $key = $namespace;
            $namespace = null;
        }

        if ($namespace) {
            Vuex::module($namespace, [$key => $this]);
        } else {
            Vuex::state([$key => $this]);
        }

        return $this;
    }
}
