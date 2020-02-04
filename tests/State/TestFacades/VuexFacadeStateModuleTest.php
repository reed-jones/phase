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
            Vuex::toArray(),
            ['state' => $data]
        );
    }

    public function test_vuex_module_matches_vuex_as_array()
    {
        $namespace = 'app';
        $data = ['works' => true];

        Vuex::module($namespace, $data);

        $this->assertSame(
            Vuex::toArray(),
            ['modules' => [
                $namespace => [
                    'state' => $data
                ]
            ]]
        );
    }

    public function test_duplicated_vuex_module_get_updated_to_the_last_value()
    {
        $namespace = 'app';

        Vuex::module($namespace, ['numbers' => 1]);
        Vuex::module($namespace, ['numbers' => 2]);
        Vuex::module($namespace, ['numbers' => 3]);
        Vuex::module($namespace, ['numbers' => 4]);
        Vuex::module($namespace, ['numbers' => 5]);

        $this->assertSame(
            Vuex::toArray(),
            ['modules' => [
                $namespace => [
                    'state' => ['numbers' => 5]
                ]
            ]]
        );
    }

    public function test_vuex_nested_modules() {
        $namespace = 'app/tests';
        $data = ['works' => 'true'];

        Vuex::module($namespace, $data);

        $this->assertSame(
            Vuex::toArray(),
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
        $data_1 = ['works' => 'false'];
        $data_2 = ['hooray' => true];
        $data_3 = ['works' => 'true'];
        $base = ['base' => 'yup'];

        Vuex::module('app', $base);

        Vuex::module($namespace, $data_1);
        Vuex::module($namespace, $data_2);
        Vuex::module($namespace, $data_3);

        $this->assertSame(
            Vuex::toArray(),
            ['modules' => [
                'app' => [
                    'state' => $base,
                    'modules' => [
                        'tests' => [
                            'state' => [
                                'works' => 'true',
                                'hooray' => true
                            ]
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
            Vuex::toArray(),
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
