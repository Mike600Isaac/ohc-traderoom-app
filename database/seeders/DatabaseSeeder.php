<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Call the MemberSeeder we built for Carrick and Sarah
        $this->call([
            MemberSeeder::class,
        ]);

        // 2. If you want a generic test user, use the new fields:
        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'current_path' => 'Free',
        ]);
    }
}