<?php

namespace Phased\Tests\Routing;

use Illuminate\View\View;
use Phased\State\Facades\Vuex;

class ResponseTest extends TestCase
{
    public function test_page_view_response()
    {
        $response = $this->get('/');
        $view = $response->getOriginalContent();

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame("<h1>Hello World</h1>\n", $view->render());
    }

    public function test_xhr_response()
    {
        $response = $this->getJson('/');

        $response->assertStatus(200);
        $response->assertJson(['$vuex' => []]);
    }
}
