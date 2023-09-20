<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\CategoryPlan;
use App\Models\MonthPlan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'api']);
        Role::create(['name' => 'user', 'guard_name' => 'api']);

        User::factory(15)->create()->each(function ($user) {
            $role = Role::inRandomOrder()->first();
            $user->assignRole($role);
        });
        Wallet::factory(60)->create();
        Category::factory(70)->create();
        Transaction::factory(400)->create();
        MonthPlan::factory(50)->create();
        CategoryPlan::factory(200)->create();
    }
}
