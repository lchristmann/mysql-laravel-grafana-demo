<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = Carbon::now()->subDays(rand(366, 731))->setTime(rand(0, 23), rand(0, 59));

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'unlocked_for_qes' => fake()->boolean(),
            'unlocked_for_valuation' => fake()->boolean(),
            'unlocked_for_multi_user_license_beta' => fake()->boolean(),
            'parent_user_id' => null,
            'last_login' => fake()->optional(0.8)->dateTimeBetween(
                fake()->boolean(80) ? '-9 days' : '-2 years',
                'now'
            ),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
