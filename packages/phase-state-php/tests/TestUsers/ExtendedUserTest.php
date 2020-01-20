<?php

namespace Phased\State\Test;

use Phased\State\Facades\Vuex;
use Phased\State\Test\Models\ExtendedUser;

class ExtendedUserTest extends TestCase
{
    public function test_an_extended_model_with_specified_module_saves_to_module() {
        ExtendedUser::find(1)->toVuex('users', 'testUser');

        $this->assertVuex(
            [
                'modules' => [
                    'users' => [
                        'state' => [ 'testUser' => ExtendedUser::find(1)->toArray() ]
                    ]
                ]
            ]
        );
    }

    public function test_all_trait_models_with_specified_module_saves_to_module() {
        ExtendedUser::all()->toVuex('users', 'allOtherUsers');

        $this->assertVuex(
            [
                'modules' => [
                    'users' => [
                        'state' => [
                            'allOtherUsers' => ExtendedUser::all()->toArray()
                        ]
                    ]
                ]
            ]
        );
    }
}
