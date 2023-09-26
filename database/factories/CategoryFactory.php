<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\URL;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->word;
        $image =  env('APP_URL') . ':' .  env('APP_PORT') . '/images/samples/categories/category-' . random_int(1, 6) . '.jpg';
        $default = fake()->randomElement([0, 1]);
        $type = fake()->randomElement(['expenses', 'incomes']);

        $userIds = User::pluck('id')->toArray();
        $randomUserId = fake()->randomElement($userIds);

        $userId = ($default === 0) ? $randomUserId : null;

        return [
            'name' => $name,
            'image' => $image,
            'type' => $type,
            'user_id' => $userId,
            'default' => $default,
        ];
    }
}
