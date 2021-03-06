<?php

namespace Phased\Tests\State;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Phased\State\Facades\Vuex;
use Phased\State\PhasedStateServiceProvider;
use PHPUnit\Framework\Constraint\IsIdentical;

class TestCase extends OrchestraTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // call migrations specific to our tests, e.g. to seed the db
        // the path option should be an absolute path.
        $this->loadMigrationsFrom(realpath(__DIR__ . '/migrations'));
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    /**
     * Load package service provider.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return Phased\State\PhasedStateServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [
            PhasedStateServiceProvider::class,
        ];
    }

    /**
     * Load package alias.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Vuex' => Vuex::class,
        ];
    }

    public static function assertVuex($data, string $message = ''): void
    {
        static::assertThat(
            Vuex::toArray(),
            new IsIdentical($data),
            $message
        );
    }
}
