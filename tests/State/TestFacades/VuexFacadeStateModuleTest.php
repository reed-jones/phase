<?php

namespace Phased\Tests\State;

use Phased\State\Facades\Vuex;

class VuexFacadeStateModuleTest extends TestCase
{
    public function test_vuex_state_matches_vuex_as_array()
    {
        $data = ['works' => true];

        Vuex::state($data);

        $this->assertSame(
            Vuex::asArray(),
            ['state' => $data]
        );
    }

    public function test_vuex_module_matches_vuex_as_array()
    {
        $namespace = 'app';
        $data = ['works' => true];

        Vuex::module($namespace, $data);

        $this->assertSame(
            Vuex::asArray(),
            ['modules' => [
                $namespace => [
                    'state' => $data
                ]
            ]]
        );
    }

    public function test_vuex_nested_modules() {
        $namespace = 'app/tests';
        $data = ['works' => 'true'];

        Vuex::module($namespace, $data);

        $this->assertSame(
            Vuex::asArray(),
            ['modules' => [
                'app' => [
                    'modules' => [
                        'tests' => [
                            'state' => $data
                        ]
                    ]
                ]
            ]]
        );
    }


    public function test_vuex_nested_modules_merge_properly() {
        $namespace = 'app/tests';
        $data_1 = ['works' => 'true'];
        $data_2 = ['hooray' => true];
        $data_3 = ['works' => 'yup'];

        Vuex::module('app', $data_3);

        Vuex::module($namespace, $data_1);

        Vuex::module($namespace, $data_2);

        $this->assertSame(
            Vuex::asArray(),
            ['modules' => [
                'app' => [
                    'state' => $data_3,
                    'modules' => [
                        'tests' => [
                            'state' => array_merge(
                                $data_1,
                                $data_2,
                            )
                        ]
                    ]
                ]
            ]]
        );
    }

    public function test_vuex_module_multiple_calls_get_merged()
    {
        $namespace = 'app';

        Vuex::module($namespace, ['works' => true]);

        Vuex::module($namespace, ['success' => 'confirmed']);

        $this->assertSame(
            Vuex::asArray(),
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

        Vuex::state(['works' => true]);

        Vuex::module($namespace, ['success' => 'confirmed']);

        $this->assertVuex(
            [
                'state' => [
                    'works' => true
                ],
                'modules' => [
                    $namespace => [
                        'state' => [ 'success' => 'confirmed' ]
                    ]
                ]
            ]
        );
    }
}
