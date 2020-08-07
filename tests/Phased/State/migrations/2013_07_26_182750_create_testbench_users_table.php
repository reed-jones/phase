<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTestbenchUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->timestamps();
        });
        Schema::create('posts', function ($table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

        $now = Carbon::now();

        DB::table('users')->insert([
            'email' => 'reed@example.com',
            'name' => 'Reed Jones',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('users')->insert([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'created_at' => $now,
            'updated_at' => $now,
        ]);


        DB::table('posts')->insert([
            'title' => 'First Great Post',
            'user_id' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
