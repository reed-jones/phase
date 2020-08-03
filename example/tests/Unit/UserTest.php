<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

it('has notices', function () {
    $user = factory(User::class)->create();
    $this->assertInstanceOf(Collection::class, $user->notices);
});

it("has products", function () {
    $user = factory(User::class)->create();
    $this->assertInstanceOf(Collection::class, $user->notices);
});
