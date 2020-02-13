<?php

namespace Phased\Tests\State;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Phased\State\Models\Model as PhaseModel;
use Phased\State\Traits\Vuexable;

class ModelTest extends TestCase
{
    public function test_an_extended_model_saves_to_namespaced_module()
    {
        ExtendedUser::find(1)->toVuex('users', 'test');

        $this->assertVuex(
            [
                'modules' => [
                    'users' => ['state' => ['test' => ExtendedUser::find(1)->toArray()]],
                ],
            ]
        );
    }

    public function test_a_trait_model_saves_to_namespaced_module()
    {
        TraitUser::find(1)->toVuex('users', 'test');

        $this->assertVuex(
            [
                'modules' => [
                    'users' => ['state' => ['test' => TraitUser::find(1)->toArray()]],
                ],
            ]
        );
    }

    public function test_all_trait_models_paginated_saves_to_module()
    {
        // LengthAwarePaginator can't seem to be extended, and missing function calls
        // ( ->toVuex() ) get passed onto the collection, so we get the paginated data
        // but we loose current_page etc. The fix, is to convert it to a collection
        // so that we can call ->toVuex() on it
        collect(ExtendedUser::paginate())->toVuex('users', 'test');

        $this->assertVuex(
            [
                'modules' => [
                    'users' => ['state' => ['test' => TraitUser::paginate()->toArray()]],
                ],
            ]
        );
    }
}

class ExtendedUser extends PhaseModel
{
    protected $table = 'users';
}

class TraitUser extends BaseModel
{
    use Vuexable;

    protected $table = 'users';
}
