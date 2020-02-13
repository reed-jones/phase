<?php

namespace Phased\Tests\State;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Phased\State\Facades\Vuex;
use Phased\State\Models\Model;
use Phased\State\Support\VuexLoader;

class VuexModuleLoaderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Vuex::register([
            ExampleModuleLoader::class,
            OtherModuleLoader::class,
        ]);
    }

    public function test_vuex_module_loader()
    {
        Vuex::load('example', 'test');

        $this->assertVuex(
            ['modules' => [
                'example' => ['state' => ['test' => 'this is good']],
            ]]
        );
    }

    public function test_vuex_module_lazy_loaded()
    {
        Vuex::lazyLoad('example', 'user');

        // Login Later
        Auth::login(User::first());

        $this->assertVuex(
            ['modules' => [
                // Just need 'toArray' for testing since otherwise
                // User::first and Auth::user have different 'references'
                'example' => ['state' => ['user' => User::first()->toArray()]],
            ]]
        );
    }

    public function test_vuex_namespace_override()
    {
        Vuex::load('blah', 'test');

        $this->assertVuex(
            ['modules' => [
                'blah' => ['state' => ['test' => 'this is good']],
            ]]
        );
    }
}

class ExampleModuleLoader extends VuexLoader
{
    public function test()
    {
        return 'this is good';
    }

    public function user()
    {
        return Auth::user()->toArray();
    }
}

class OtherModuleLoader extends VuexLoader
{
    protected $namespace = 'blah';

    public function test()
    {
        return 'this is good';
    }
}

class User extends Authenticatable
{
    //
}
