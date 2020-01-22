<?php

namespace Phased\Tests\State;

use Phased\State\Facades\Vuex;
use Phased\Tests\State\Models\TraitUser;

class TraitUserTest extends TestCase
{
    public function test_a_trait_model_with_specified_module_saves_to_module() {
        TraitUser::find(1)->toVuex('users', 'testUser');

        $this->assertVuex(
            [
                'modules' => [
                    'users' => [
                        'state' => [
                            'testUser' => TraitUser::find(1)->toArray()
                        ]
                    ]
                ]
            ]
        );
    }

    public function test_all_trait_models_with_specified_module_saves_to_module() {
        TraitUser::all()->toVuex('users', 'allOtherUsers');

        $this->assertVuex(
            [
                'modules' => [
                    'users' => [
                        'state' => [
                            'allOtherUsers' => TraitUser::all()->toArray()
                        ]
                    ]
                ]
            ]
        );
    }

    public function test_all_trait_models_paginated_saves_to_module() {
        // LengthAwarePaginator can't seem to be extended, and missing function calls
        // ( ->toVuex() ) get passed onto the collection, so we get the paginated data
        // but we loose current_page etc. The fix, is to convert it to a collection
        // so that we can call ->toVuex() on it
        collect(TraitUser::paginate())->toVuex('users', 'allUsers');

        $this->assertVuex(
            [
                'modules' => [
                    'users' => [
                        'state' => [
                            'allUsers' => TraitUser::paginate()->toArray()
                        ]
                    ]
                ]
            ]
        );
    }
}
