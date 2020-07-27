<?php

use App\Models\Product;
use App\Models\User;

it('belongs to a user', function () {
    $product = factory(Product::class)->create();
    $this->assertInstanceof(User::class, $product->user);
});
