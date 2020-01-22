<?php

declare(strict_types=1);

namespace Phased\State\Mixins;

use Exception;
use Phased\State\Facades\Vuex;

class VuexResponseMixin
{
    public function phase()
    {
        return function (array $data = [], int $status = 200, array $headers = [], int $options = 0) {
            try {
                $data = array_merge(['$vuex' => Vuex::asArray()], $data);
                return $this->json($data, $status, $headers, $options);
            } catch (Exception $err) {
                return $this->json([
                    'phase_error' => 'An error occurred while phasing'
                ], 422);
            }
        };
    }
}
