<?php

declare(strict_types=1);

namespace Phased\State\Mixins;

use Exception;
use Phased\State\Facades\Vuex;

class VuexResponseMixin
{
    public function phase()
    {
        return $this->phaseResponse();
    }

    public function vuex()
    {
        return $this->phaseResponse();
    }

    private function phaseResponse()
    {
        return function (array $data = [], int $status = 200, array $headers = [], int $options = 0) {
            try {
                $data = array_merge(['$vuex' => Vuex::toArray()], $data);

                return $this->json($data, $status, $headers, $options);
            } catch (Exception $err) {
                return $this->json([
                    'phase_error' => 'An error occurred while phasing',
                ], 422);
            }
        };
    }
}
