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
        Role::factory(2)->create();
        User::factory(20)->create()->each(function ($user) {
            $role = Role::inRandomOrder()->first();
            $user->assignRole($role);
        });
        Wallet::factory(30)->create();
        Category::factory(50)->create();
        Transaction::factory(200)->create();
        MonthPlan::factory(50)->create();
        CategoryPlan::factory(200)->create();
    }
}
