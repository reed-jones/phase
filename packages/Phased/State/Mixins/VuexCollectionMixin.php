<?php

declare(strict_types=1);

namespace Phased\State\Mixins;

use Phased\State\Exceptions\VuexInvalidKeyException;
use Phased\State\Facades\Vuex;

class VuexCollectionMixin
{
    public function toVuex()
    {
        return function ($namespace, $key = null) {
            if (!$namespace) {
                throw new VuexInvalidKeyException("Could not determine where to store the data");
            }

            // if only one key is provided, it will get saved
            // to the base state (non-module)
            if (!$key) {
                $key = $namespace;
                $namespace = null;
            }

            if ($namespace) {
                Vuex::module($namespace, [$key => $this]);
            } else {
                Vuex::state([$key => $this]);
            }

            return $this;
        };
    }
}
