<?php

it("returns a 200", function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});
