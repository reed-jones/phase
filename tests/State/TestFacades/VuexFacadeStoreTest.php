<?php

namespace Phased\Tests\State;

use Phased\State\Facades\Vuex;

class VuexFacadeStoreTest extends TestCase
{
    public function test_vuex_store_state_matches_vuex_as_array()
    {
        $data = ['works' => true];

        Vuex::store(function ($store) use ($data) {
            $store->state($data);
        });

        $this->assertVuex(
            ['state' => $data]
        );
    }

    public function test_vuex_store_module_matches_vuex_as_array()
    {
        $namespace = 'app';
        $data = ['works' => true];

        Vuex::store(function ($store) use ($namespace, $data) {
            $store->module($namespace, $data);
        });

        $this->assertVuex(
            ['modules' => [
                $namespace => [
                    'state' => $data
                ]
            ]]
        );
    }

    public function test_vuex_store_multiple_module_calls_get_merged()
    {
        $namespace = 'app';

        Vuex::store(function ($store) use ($namespace) {
            $store->module($namespace, ['works' => true]);
        });

        Vuex::store(function ($store) use ($namespace) {
            $store->module($namespace, ['success' => 'confirmed']);
        });

        $this->assertVuex(
            ['modules' => [
                $namespace => [
                    'state' => [
                        'works' => true,
                        'success' => 'confirmed',
                    ]
                ]
            ]]
        );
    }

    public function test_vuex_store_state_and_modules_can_coexist()
    {
        $namespace = 'app';

        Vuex::store(function ($store) {
            $store->state(['works' => true]);
        });

        Vuex::store(function ($store) use ($namespace) {
            $store->module($namespace, ['success' => 'confirmed']);
        });

        $this->assertVuex(
            [
                'state' => [
                    'works' => true
                ],
                'modules' => [
                    $namespace => [
                        'state' => [
                            'success' => 'confirmed',
                        ]
                    ]
                ]
            ]
        );
    }
}
