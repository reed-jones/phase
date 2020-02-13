<?php

namespace Phased\Tests\State;

use Illuminate\Support\Facades\Route;
use Phased\State\Facades\Vuex;

class VuexFacadeReturnTypesTest extends TestCase
{
    public function test_vuex_as_json_is_json_string()
    {
        $data = ['works' => true];

        Vuex::state($data);

        $this->assertSame(
            '{"state":{"works":true}}',
            Vuex::toJson(),
        );
    }

    public function test_vuex_as_response_is_json_response()
    {
        Route::get('/get-json', function () {
            $data = ['works' => true];
            Vuex::state($data);

            return Vuex::toResponse();
        });

        $response = $this->get('/get-json');

        $response->assertJson(
            ['$vuex' => ['state' => ['works' => true]]]
        );
    }

    public function test_as_array_with_no_data_returns_empty_array()
    {
        $this->assertVuex([]);
    }
}
