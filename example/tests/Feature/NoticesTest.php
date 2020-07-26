<?php

use App\Models\Notice;
use App\Models\User;

/** reseed before every test */
beforeEach(fn () => $this->seed());

it("can be created by an authenticated user", function () {
    $this->withoutExceptionHandling();

    $this->actingAs(factory(User::class)->create());

   $attributes = factory(Notice::class)->raw(['user_id' => null]);
   unset($attributes['user_id']);

    $postResponse = $this->postJson('/api/v1/notices', $attributes)
        ->assertStatus(201)
        ->assertJson([
            'created' => true,
            'notice' => [ 'id' => true , 'created_at' => true ]
        ]);

    $this->assertDatabaseHas('notices', $attributes);

    $getResponse = $this->getJson("/api/v1/notices/{$postResponse['notice']['id']}")
        ->assertStatus(200)
        ->assertJson([
            'notice' => [ 'id' => true , 'created_at' => true ]
        ]);
});

it('can not be created if it fails validation', function () {
    $this->actingAs(factory(User::class)->create());
    $notice = factory(Notice::class)->raw([ 'title' => null, 'content' => null ]);
    $this->postJson('/api/v1/notices', $notice)
        ->assertStatus(422)
        ->assertJson(['errors' => [ 'title' => true, 'content' => true ]]);
});

it('can not be created by a guest')
    ->postJson('/api/v1/notices', [])
    ->assertStatus(401);

it("can be updated by the user who created it", function () {
    $user = factory(User::class)->state('makes-notices')->create();
    $notice = $user->notices->random();
    $this->actingAs($user);

    $nextTitle = $this->faker->sentence($this->faker->numberBetween(1, 5));

    $this->putJson("/api/v1/notices/{$notice->id}", ['title' => $nextTitle])
        ->assertStatus(200);

    $this->getJson("/api/v1/notices/{$notice->id}")
        ->assertStatus(200)
        ->assertJson([ 'notice' => [ 'title' => $nextTitle ] ]);


    $nextContent = $this->faker->paragraph($this->faker->numberBetween(2, 10));

    $this->putJson("/api/v1/notices/{$notice->id}", ['content' => $nextContent])
        ->assertStatus(200);

    $this->getJson("/api/v1/notices/{$notice->id}")
        ->assertStatus(200)
        ->assertJson([ 'notice' => [ 'content' => $nextContent ] ]);
});
it("can not be updated by a user who did not create it", function () {
    $user = factory(User::class)->state('makes-notices')->create();
    $notice = $user->notices->random();
    $this->actingAs(factory(User::class)->create());

    $nextContent = $this->faker->paragraph($this->faker->numberBetween(2, 10));
    $this->putJson("/api/v1/notices/{$notice->id}", ['content' => $nextContent])
        ->assertStatus(403);
});
it('can not be updated by a guest')
    ->patchJson('/api/v1/notices/1', [])
    ->assertStatus(401);

it("can be deleted by the user who created it", function () {
    $user = factory(User::class)->state('makes-notices')->create();
    $this->actingAs($user);
    $notice = $user->notices->random();
    $this->deleteJson("/api/v1/notices/{$notice->id}")
        ->assertStatus(204);
});
it("can not be deleted by a user who did not created it", function () {
    $user = factory(User::class)->state('makes-notices')->create();
    $notice = $user->notices->random();
    $this->actingAs(factory(User::class)->create());

    $nextContent = $this->faker->paragraph($this->faker->numberBetween(2, 10));
    $this->deleteJson("/api/v1/notices/{$notice->id}", ['content' => $nextContent])
        ->assertStatus(403);
});
it('can not be deleted by a guest')
    ->deleteJson('/api/v1/notices/1', [])
    ->assertStatus(401);

it("returns listing to guests without contact information", function () {
    $response = $this->getJson('/api/v1/notices')
        ->assertStatus(200)
        ->assertJson([
            'notices' => [
                'data' => true,
                'from' => true,
                'to' => true,
                'total' => true
            ]
        ]);

        $notice  = collect($response['notices']['data'])->first();
        $this->assertArrayHasKey('id', $notice);
        $this->assertArrayNotHasKey('user', $notice);
});

it("returns listing to authenticated users with contact information", function () {
    $this->actingAs(factory(User::class)->create());

    $response = $this->getJson('/api/v1/notices')
        ->assertStatus(200)
        ->assertJson([
            'notices' => [
                'data' => true,
                'from' => true,
                'to' => true,
                'total' => true
            ]
        ]);

        $notice  = collect($response['notices']['data'])->first();
        $this->assertArrayHasKey('id', $notice);
        $this->assertArrayHasKey('user', $notice);
});

it("returns details to guests without contact information", function () {
    $notice = factory(Notice::class)->create();

    $response = $this->getJson("/api/v1/notices/{$notice->id}")
        ->assertStatus(200)
        ->assertJson([ 'notice' => true ]);

    $this->assertArrayHasKey('id', $response['notice']);
    $this->assertArrayNotHasKey('user', $response['notice']);
});
it("returns details to authenticated users with contact information", function () {
    $user = factory(User::class)->create();
    $this->actingAs($user);
    $notice = factory(Notice::class)->create();

    $response = $this->getJson("/api/v1/notices/{$notice->id}")
        ->assertStatus(200)
        ->assertJson([ 'notice' => true ]);

    $this->assertArrayHasKey('id', $response['notice']);
    $this->assertArrayHasKey('user', $response['notice']);
});
