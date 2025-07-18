<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Protocol>
 */
class ProtocolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Assume that protocols can just exist too, without being signed with QES.
        // Only for those that area signed with QES, we need to have a user that has qes enabled.
        $signedWithQes = $this->faker->boolean();
        $createdAt = Carbon::now()->subDays(rand(0, 365))->setTime(rand(0, 23), rand(0, 59));

        if ($signedWithQes) {
            $user = User::where('unlocked_for_qes', true)->inRandomOrder()->first();
            return [
                'user_id' => $user?->id,
                'title' => fake()->words(2, true),
                'signed_with_qes_at' => $user
                    ? $createdAt->addMinutes(40)
                    : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        } else {
            return [
                'user_id' => User::inRandomOrder()->first()?->id,
                'title' => fake()->words(2, true),
                'signed_with_qes_at' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }
    }
}
