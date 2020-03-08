<?php

namespace Phased\Tests\Routing;

use Illuminate\Support\Facades\Artisan;

class ConsoleTest extends TestCase
{
    public function test_artisan_command_table()
    {
        Artisan::call('phase:routes');

        $resultAsText = Artisan::output();

        $this->assertSame(
            "+--------------+-----+---------------------------+------------+\n".
            "| Group Prefix | URI | Navigation Name           | Middleware |\n".
            "+--------------+-----+---------------------------+------------+\n".
            "|              | /   | TestController@HelloWorld |            |\n".
            "+--------------+-----+---------------------------+------------+\n",
            $resultAsText);
    }

    public function test_artisan_command_json()
    {
        Artisan::call('phase:routes --json');

        $resultAsText = Artisan::output();

        $this->assertSame(
            '[{"prefix":null,"uri":"\/","name":"TestController@HelloWorld","middleware":""}]'."\n",
            $resultAsText);
    }

    public function test_artisan_command_json_config()
    {
        Artisan::call('phase:routes --json --config');

        $resultAsText = Artisan::output();

        $configKeys = array_keys(json_decode($resultAsText, true)['config']);

        $this->assertSame(
            [
                'entry',
                'state',
                'initial_state_key',
                'initial_state_id',
                'redirects',
                'ssr',
                'hydrate',
                'assets',
            ],
            $configKeys
        );
    }
}
