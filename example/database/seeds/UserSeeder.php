<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->states(['makes-notices', 'sells-products'])
            ->create(['name' => 'Reed Jones', 'email' => 'reedjones@reedjones.com', 'password' => Hash::make('password')]);

        factory(User::class, 15)->states('makes-notices')->create();

        factory(User::class, 15)->states('sells-products')->create();

        factory(User::class, 15)->states('makes-notices', 'sells-products')->create();

        factory(User::class, 15)->create();
    }
}
