<?php

use Illuminate\View\View;
use Phased\State\Facades\Vuex;

it('returns a blade view as the standard response', function () {
    $response = $this->get('/');
    $view = $response->getOriginalContent();

    assertInstanceOf(View::class, $view);
    assertSame("<script id='phase-state'>window.__PHASE_STATE__=[]</script>", $view->render());
});

it('returns a blade view with initial state', function () {
    $response = $this->get('/');
    $view = $response->getOriginalContent();
    Vuex::state(['operation' => 'successful']);

    assertInstanceOf(View::class, $view);
    assertSame("<script id='phase-state'>window.__PHASE_STATE__={\"state\":{\"operation\":\"successful\"}}</script>", $view->render());
});

it('returns json for an xhr response', function () {
    $response = $this->getJson('/');

    $response->assertStatus(200);
    $response->assertJson(['$vuex' => []]);
});
