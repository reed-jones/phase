<?php

namespace Phased\Tests\State;

use Phased\State\Facades\Vuex;

class VuexFacadeLazyEvaluationTest extends TestCase
{
    /**
     * @test
     * @dataProvider getState
     */
    public function test_vuex_state_get_lazy_evaluation(...$args)
    {
        [$state, $expected] = $args;
        Vuex::state($state);

        $this->assertVuex([ 'state' => $expected ]);
    }

    /**
     * @test
     * @dataProvider getModule
     */
    public function test_vuex_modules_get_lazy_evaluation(...$args)
    {
        [$namespace, $state, $expected] = $args;

        Vuex::module($namespace, $state);

        $this->assertVuex($expected);
    }

    public function getState()
    {
        return [
            [
                function () {
                    return [
                        'numbers' => [1, 2, 3, 4, 5],
                        'string' => 'hello world'
                    ];
                },
                [
                    'numbers' => [1, 2, 3, 4, 5],
                    'string' => 'hello world'
                ]
            ],
            [
                [
                    'numbers' => [1, 2, 3, 4, 5],
                    'string' => function () {
                        return 'hello world';
                    }
                ],
                [
                    'numbers' => [1, 2, 3, 4, 5],
                    'string' => 'hello world'
                ]
            ],
            [
                [
                    'numbers' => function () {
                        return [1, 2, 3, 4, 5];
                    },
                    'string' => function () {
                        return 'hello world';
                    }
                ],
                [
                    'numbers' => [1, 2, 3, 4, 5],
                    'string' => 'hello world'
                ]
            ]
        ];
    }

    public function getModule()
    {
        return [
            [
                'numbers',
                function () {
                    return [
                        'test' => 5,
                        'great' => ['your', 'telling', 'me']
                    ];
                },
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
            ],
            [
                'numbers/testing', function () {
                    return [
                        'test' => 5,
                        'great' => ['your', 'telling', 'me']
                    ];
                },
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
            ],
            [
                'numbers', [
                    'test' => 5,
                    'great' => function () {
                        return ['your', 'telling', 'me'];
                    }
                ],
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
            ],
            [
                'numbers/testing', [
                    'test' => 5,
                    'great' => function () {
                        return ['your', 'telling', 'me'];
                    }
                ],
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
            ]
        ];
    }
}
