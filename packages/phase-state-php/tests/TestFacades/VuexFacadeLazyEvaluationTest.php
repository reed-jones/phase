<?php

namespace Phased\State\Test;

use Phased\State\Facades\Vuex;

class VuexFacadeLazyEvaluationTest extends TestCase
{
    public function test_vuex_state_get_lazy_evaluation()
    {
        Vuex::state(function () {
            return [
                'numbers' => [1,2,3,4,5],
                'string' => 'hello world'
            ];
        });

        $this->assertVuex([
            'state' => [
                'numbers' => [1,2,3,4,5],
                'string' => 'hello world'
            ]
        ]);
    }

    public function test_vuex_state_get_nested_lazy_evaluation()
    {
        Vuex::state([
                'numbers' => [1,2,3,4,5],
                'string' => function () {
                    return 'hello world';
                }
        ]);

        $this->assertVuex([
            'state' => [
                'numbers' => [1,2,3,4,5],
                'string' => 'hello world'
            ]
        ]);
    }

    public function test_vuex_modules_get_lazy_evaluation()
    {
        Vuex::module('numbers', function () {
            return [
                'test' => 5,
                'great' => ['your', 'telling', 'me']
            ];
        });

        $this->assertVuex(
            [
                'modules' => [
                    'numbers' => [
                        'state' => [
                            'test' => 5,
                            'great' => ['your', 'telling', 'me']
                        ]
                    ]
                ]
            ]
        );
    }

    public function test_vuex_nested_modules_get_lazy_evaluation()
    {
        Vuex::module('numbers/testing', function () {
            return [
                'test' => 5,
                'great' => ['your', 'telling', 'me']
            ];
        });

        $this->assertVuex(
            [
                'modules' => [
                    'numbers' => [
                        'modules' => [
                            'testing' => [
                                'state' => [
                                    'test' => 5,
                                    'great' => ['your', 'telling', 'me']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
    }

    public function test_vuex_modules_get_second_level_lazy_evaluation()
    {
        Vuex::module('numbers', [
            'test' => 5,
            'great' => function () {
                return ['your', 'telling', 'me'];
            }
        ]);

        $this->assertVuex(
            [
                'modules' => [
                    'numbers' => [
                        'state' => [
                            'test' => 5,
                            'great' => ['your', 'telling', 'me']
                        ]
                    ]
                ]
            ]
        );
    }


    public function test_vuex_nested_modules_get_second_level_lazy_evaluation()
    {
        Vuex::module('numbers/testing', [
            'test' => 5,
            'great' => function () {
                return ['your', 'telling', 'me'];
            }
        ]);

        $this->assertVuex(
            [
                'modules' => [
                    'numbers' => [
                        'modules' => [
                            'testing' => [
                                'state' => [
                                    'test' => 5,
                                    'great' => ['your', 'telling', 'me']
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
    }
}
