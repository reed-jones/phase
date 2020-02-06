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
        $this->assertSame("<script id='phase-state'>window.__PHASE_STATE__=[]</script>", $view->render());
    }

    public function test_page_view_with_state_response()
    {
        $response = $this->get('/');
        $view = $response->getOriginalContent();
        Vuex::state(['operation' => 'successful']);

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame("<script id='phase-state'>window.__PHASE_STATE__={\"state\":{\"operation\":\"successful\"}}</script>", $view->render());
    }

    public function test_xhr_response()
    {
        $response = $this->getJson('/');

        $response->assertStatus(200);
        $response->assertJson(['$vuex' => []]);
    }
}
