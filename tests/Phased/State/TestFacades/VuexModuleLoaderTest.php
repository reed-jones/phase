<?php

namespace Phased\Tests\State;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Phased\State\Exceptions\VuexMissingRequiredParameter;
use Phased\State\Facades\Vuex;
use Phased\State\Support\VuexLoader;
beforeEach(function () {
    Vuex::register([
        ExampleModuleLoader::class,
        OtherModuleLoader::class,
    ]);
});


it('loads simple string (no args)', function () {
    Vuex::load('example', 'test');

    $this->assertVuex(
        ['modules' => [
            'example' => ['state' => ['test' => 'this is good']],
        ]]
    );
});

it('lazy loads simple strings (no args)', function () {
    Vuex::lazyLoad('example', 'auth');

    // Login Later
    $this->actingAs(User::first());

    $this->assertVuex(
        ['modules' => [
            // Just need 'toArray' for testing since otherwise
            // User::first and Auth::user have different 'references'
            'example' => ['state' => ['auth' => User::first()->toArray()]],
        ]]
    );
});

it('uses the namespace override when requested', function () {
    Vuex::load('blah', 'test');

    $this->assertVuex(
        ['modules' => [
            'blah' => ['state' => ['test' => 'this is good']],
        ]]
    );
});

it('loads multiple modules in array', function () {
    Vuex::load('blah', ['test', 'otherTest']);

    $this->assertVuex(
        ['modules' => [
            'blah' => ['state' => ['test' => 'this is good', 'otherTest' => 12345]],
        ]]
    );
});

it('loads a simple string with arbitrary number of args', function () {
    Vuex::load('example', 'sum', 1, 2, 3, 4, 5);

    $this->assertVuex(
        ['modules' => [
            'example' => ['state' => ['sum' => 15 ]],
        ]]
    );
});

it('complex modules, with and without arguments', function () {
    $this->actingAs(User::first());

    Vuex::load('example', [
        'sum' => [1, 2, 3, 4, 5, 10],
        'number' => 15,
        'test',
        'auth'
    ]);

    $this->assertVuex(
        ['modules' => [
            'example' => ['state' => [
                'sum' => 25,
                'number' => 15,
                'test' => 'this is good',
                'auth' => User::first()->toArray()
            ]],
        ]]
    );
});

it('can mix and match multiple modules', function () {
    Vuex::load('example', 'test');
    Vuex::lazyLoad('blah', 'test');

    $this->assertVuex(
        ['modules' => [
            'example' => ['state' => ['test' => 'this is good']],
            'blah' => ['state' => ['test' => 'this is good']],
        ]]
    );
});

it('uses dependency injection (same order)', function () {
    Vuex::load('example', [
        'blog' => ['post' => 1, 'user' => 2],
    ]);

    $this->assertVuex(
        ['modules' => [
            'example' => ['state' => [ 'blog' => [
                    'user' => [ 'id' => 2, 'name' => 'Test User' ],
                    'post' => [ 'id' => 1, 'title' => 'First Great Post', 'user_id' => '1' ],
                    'url' => '/',
                    'number' => null
                ]
            ]],
        ]]
    );
});

it('uses dependency injection (opposite order)', function () {
    Vuex::load('example', [
        'blog' => ['user' => 2, 'post' => 1, 'number' => 10],
    ]);

    $this->assertVuex(
        ['modules' => [
            'example' => ['state' => [ 'blog' => [
                'user' => [ 'id' => 2, 'name' => 'Test User' ],
                'post' => [ 'id' => 1, 'title' => 'First Great Post', 'user_id' => '1' ],
                'url' => '/',
                'number' => 10
            ]]],
        ]]
    );
});

it('uses dependency injection (missing param exception)', function () {
    $this->expectException(VuexMissingRequiredParameter::class);

    Vuex::load('example', [
        'blog' => [ 'user' => 2 ],
    ]);
});


it('allows full model instead of dependency injection', function () {
    Vuex::load('example', [
        'blog' => [
            'user' => User::find(2),
            'post' => 1,
            'number' => 5
        ],
    ]);

    $this->assertVuex([
        'modules' => [
            'example' => [
                'state' => [
                    'blog' => [
                        'user' => [ 'id' => 2, 'name' => 'Test User' ],
                        'post' => [ 'id' => 1, 'title' => 'First Great Post', 'user_id' => '1' ],
                        'url' => '/',
                        'number' => 5
                    ]
                ]
            ],
        ]
    ]);
});


it('inject multiple of the same model based one key name', function () {
    Vuex::load('example', [
        'getTwoUsers' => [
            'userTwo' => 2,
            'userOne' => 1,
        ],
    ]);

    $this->assertVuex(
        ['modules' => [
            'example' => ['state' => [ 'getTwoUsers' => [
                    'userOne' => [ 'id' => 1, 'name' => 'Reed Jones' ],
                    'userTwo' => [ 'id' => 2, 'name' => 'Test User' ],
                ]
            ]],
        ]]
    );
});

class ExampleModuleLoader extends VuexLoader
{
    public function test()
    {
        return 'this is good';
    }

    public function auth()
    {
        return Auth::user()->toArray();
    }

    public function blog(Post $post, User $user, Request $request, int $number = null)
    {
        return [
            'user' => $user->only('id', 'name'),
            'post' => $post->only('id', 'title', 'user_id'),
            'url' => $request->getPathInfo(),
            'number' => $number
        ];
    }

    public function getTwoUsers(User $userOne, User $userTwo)
    {
        return [
            'userOne' => $userOne->only('id', 'name'),
            'userTwo' => $userTwo->only('id', 'name')
        ];
    }

    public function sum(...$numbers) {
        $total = 0;
        foreach($numbers as $number) {
            $total += $number;
        }

        return $total;
    }

    public function number($num) {
        return $num;
    }
}

class OtherModuleLoader extends VuexLoader
{
    protected $namespace = 'blah';

    public function test()
    {
        return 'this is good';
    }

    public function otherTest()
    {
        return 12345;
    }
}

class User extends Authenticatable
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

class Post extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
