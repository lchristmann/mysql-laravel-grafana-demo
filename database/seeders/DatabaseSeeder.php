<?php

namespace Database\Seeders;

use App\Models\Protocol;
use App\Models\User;
use App\Models\Valuation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->count(40)->create();
        User::factory()->count(10)->state(['unlocked_for_qes' => true, 'unlocked_for_valuation' => true])->create();
        User::factory()
            ->count(10)
            ->state(['unlocked_for_multi_user_license_beta' => true])
            ->create()
            ->each(function ($parent) {
                // For each parent, create 2-4 sub-users
                User::factory()
                    ->count(rand(2, 4))
                    ->create([
                       'parent_user_id' => $parent->id
                    ]);
            });

        Protocol::factory()->count(400)->create();
        Valuation::factory()->count(400)->create();
    }
}
