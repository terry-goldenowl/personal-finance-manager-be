<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userIds = User::pluck('id')->toArray();
        $randomUserId = fake()->randomElement($userIds);

        $isAlreadyHaveDefault = Wallet::where(['user_id' => $randomUserId, 'default' => 1])->exists();
        $default = !$isAlreadyHaveDefault;

        do {
            $name = fake()->word;
        } while (Wallet::where(['user_id' => $randomUserId, 'name' => 'name'])->exists());

        return [
            'name' => $name,
            'user_id' => $randomUserId,
            'image' => '/storage/images/sample/sample-wallet.jpg',
            'default' => $default,
        ];
    }
}
