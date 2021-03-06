<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(AccountTypeSeeder::class);
        $this->call(ExpenseTypeSeeder::class);
        // \App\Models\User::factory(10)->create();
    }
}
