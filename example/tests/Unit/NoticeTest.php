<?php

use App\Models\Notice;
use App\Models\User;

it("belongs to a user", function () {
    $notice = factory(Notice::class)->create();
    $this->assertInstanceOf(User::class, $notice->user);
});
