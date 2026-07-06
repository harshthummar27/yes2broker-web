<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('amenity_options')) {
            Schema::create('amenity_options', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('icon')->default('default');
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (DB::table('amenity_options')->count() > 0) {
            return;
        }

        $now = now();

        $amenities = [
            ['name' => '24x7 Security', 'icon' => 'security'],
            ['name' => 'CCTV Surveillance', 'icon' => 'cctv'],
            ['name' => 'Fire Safety System', 'icon' => 'fire'],
            ['name' => 'Intercom Facility', 'icon' => 'intercom'],
            ['name' => 'Gated Community', 'icon' => 'gate'],
            ['name' => 'Entrance Gate with Security Cabin', 'icon' => 'gate-security'],
            ['name' => 'Car Parking', 'icon' => 'car-parking'],
            ['name' => 'Visitor Parking', 'icon' => 'parking'],
            ['name' => 'Lift / Elevators', 'icon' => 'elevator'],
            ['name' => 'Power Backup (Common Areas)', 'icon' => 'power'],
            ['name' => '24x7 Water Supply', 'icon' => 'water'],
            ['name' => 'EV Charging Provision', 'icon' => 'ev-charge'],
            ['name' => 'Bicycle Parking', 'icon' => 'bicycle'],
            ['name' => 'Clubhouse', 'icon' => 'clubhouse'],
            ['name' => 'Gymnasium', 'icon' => 'gym'],
            ['name' => 'Children\'s Play Area', 'icon' => 'playground'],
            ['name' => 'Landscaped Garden', 'icon' => 'garden'],
            ['name' => 'Walking Track', 'icon' => 'walk'],
            ['name' => 'Seating Area', 'icon' => 'seating'],
            ['name' => 'Senior Citizen Sitting Area', 'icon' => 'seniors'],
            ['name' => 'Community Hall / Multipurpose Hall', 'icon' => 'hall'],
            ['name' => 'Indoor Games Room', 'icon' => 'games'],
            ['name' => 'Wi-Fi in Common Areas', 'icon' => 'wifi'],
            ['name' => 'Rainwater Harvesting', 'icon' => 'rain'],
            ['name' => 'Solar Lighting for Common Areas', 'icon' => 'solar'],
            ['name' => 'Waste Management System', 'icon' => 'waste'],
        ];

        DB::table('amenity_options')->insert(array_map(
            fn (array $amenity, int $index) => [
                'name' => $amenity['name'],
                'slug' => Str::slug($amenity['name']),
                'icon' => $amenity['icon'],
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $amenities,
            array_keys($amenities)
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('amenity_options');
    }
};
