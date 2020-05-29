<?php

namespace Phased\Tests\State;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Phased\State\Models\Model as PhaseModel;
use Phased\State\Traits\Vuexable;


it('makes extended models vuexable', function () {
    ExtendedUser::find(1)->toVuex('users', 'test');

    $this->assertVuex([
        'modules' => [
            'users' => ['state' => ['test' => ExtendedUser::find(1)->toArray()]],
        ],
    ]);
});

it('makes trait models vuexable', function () {
    TraitUser::find(1)->toVuex('users', 'test');

    $this->assertVuex(
        [
            'modules' => [
                'users' => ['state' => ['test' => TraitUser::find(1)->toArray()]],
            ],
        ]
    );
});

it('collects paginated data to vuex module', function () {
    // LengthAwarePaginator can't seem to be extended, and missing function calls
    // ( ->toVuex() ) get passed onto the collection, so we get the paginated data
    // but we loose current page etc. The fix, is to convert it to a collection
    // so that we can call ->toVuex() on it
    collect(ExtendedUser::paginate())->toVuex('users', 'test');

    $this->assertVuex(
        [
            'modules' => [
                'users' => ['state' => ['test' => ExtendedUser::paginate()->toArray()]],
            ],
        ]
    );
});


class ExtendedUser extends PhaseModel
{
    protected $table = 'users';
}

class TraitUser extends BaseModel
{
    use Vuexable;

    protected $table = 'users';
}
