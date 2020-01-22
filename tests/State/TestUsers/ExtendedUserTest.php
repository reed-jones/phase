<?php

namespace Phased\Tests\State;

use Phased\State\Facades\Vuex;
use Phased\Tests\State\Models\ExtendedUser;

class ExtendedUserTest extends TestCase
{
    public function test_an_extended_model_with_specified_module_saves_to_module() {
        ExtendedUser::find(1)->toVuex('users', 'test');

        $this->assertVuex(
            [
                'modules' => [
                    'users' => [
                        'state' => [ 'test' => ExtendedUser::find(1)->toArray() ]
                    ]
                ]
            ]
        );
    }

    public function test_all_trait_models_with_specified_module_saves_to_module() {
        ExtendedUser::all()->toVuex('users', 'test');

        $this->assertVuex(
            [
                'modules' => [
                    'users' => [
                        'state' => [
                            'test' => ExtendedUser::all()->toArray()
                        ]
                    ]
                ]
            ]
        );
    }
}
