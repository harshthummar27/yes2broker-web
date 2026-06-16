<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
        ]);

        if (is_file(base_path('properties_new.sql'))
            && is_file(base_path('wp_posts.csv'))
            && is_file(base_path('Properties-Export-2026-June-16-1029.csv'))) {
            $this->call(LegacyPropertySeeder::class);
        } else {
            $this->call(PropertySeeder::class);
        }
    }
}
