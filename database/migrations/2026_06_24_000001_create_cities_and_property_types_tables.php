<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('property_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('filter_keyword')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('cities')->insert([
            [
                'name' => 'Ahmedabad',
                'slug' => 'ahmedabad',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Gandhinagar',
                'slug' => 'gandhinagar',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('property_types')->insert([
            ['name' => 'Apartment', 'slug' => 'apartment', 'filter_keyword' => 'bhk', 'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Villa', 'slug' => 'villa', 'filter_keyword' => 'villa', 'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Home', 'slug' => 'home', 'filter_keyword' => 'home', 'sort_order' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bungalow', 'slug' => 'bungalow', 'filter_keyword' => 'bungalow', 'sort_order' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Office', 'slug' => 'office', 'filter_keyword' => 'office', 'sort_order' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Showroom', 'slug' => 'showroom', 'filter_keyword' => 'showroom', 'sort_order' => 6, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Shop', 'slug' => 'shop', 'filter_keyword' => 'shop', 'sort_order' => 7, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'FarmHouse', 'slug' => 'farmhouse', 'filter_keyword' => 'farmhouse', 'sort_order' => 8, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Land', 'slug' => 'land', 'filter_keyword' => 'plot', 'sort_order' => 9, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('property_types');
        Schema::dropIfExists('cities');
    }
};
