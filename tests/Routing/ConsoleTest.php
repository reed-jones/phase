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

        $this->assertSame(
            '{"config":{"entry":"phase::app","redirects":{"401":"Auth.LoginPage","403":"Auth.LoginPage","404":"Errors.MissingPage","500":"Errors.ServerError"},"ssr":false,"assets":{"js":["js\/app.js"],"sass":["sass\/app.scss"]}},"routes":[{"prefix":null,"uri":"\/","name":"TestController@HelloWorld","middleware":""}]}'."\n",
            $resultAsText);
    }
}
