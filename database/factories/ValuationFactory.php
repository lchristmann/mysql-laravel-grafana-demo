<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Valuation>
 */
class ValuationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = Carbon::now()->subDays(rand(0, 365))->setTime(rand(0, 23), rand(0, 59));

        $user = User::where('unlocked_for_valuation', true)->inRandomOrder()->first();
        return [
            'user_id' => $user?->id,
            'price_in_cents' => fake()->numberBetween(400, 6_000),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
