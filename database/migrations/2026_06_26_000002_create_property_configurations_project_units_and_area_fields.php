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
        if (! Schema::hasTable('property_configurations')) {
            Schema::create('property_configurations', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('project_units')) {
            Schema::create('project_units', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('properties', 'project_area_value')) {
            Schema::table('properties', function (Blueprint $table): void {
                $table->decimal('project_area_value', 12, 4)->nullable()->after('area');
                $table->string('project_area_unit')->nullable()->after('project_area_value');
            });
        }

        $now = now();

        if (DB::table('property_configurations')->count() === 0) {
            $configurations = [
                '1 BHK Apartment',
                '2 BHK Apartment',
                '2 & 3 BHK Apartments',
                '3 BHK Apartment',
                '3 & 4 BHK Apartments',
                '3 BHK & 4 BHK Apartments/Penthouses',
                '4 BHK Apartment',
                '4 BHK Apartments',
                '4 BHK Apartments/Penthouses',
                'Shop, 3 & 4 BHK Apartments',
                'Commercial Shops',
                'Office Space',
                'Showroom',
                'Residential Plots',
                'Villa',
                'Bungalow',
                'Penthouse',
                'Farmhouse',
            ];

            DB::table('property_configurations')->insert(array_map(
                fn (string $name, int $index) => [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                $configurations,
                array_keys($configurations)
            ));
        }

        if (DB::table('project_units')->count() === 0) {
            $units = [
                'Sq. Yard.',
                'Sq. mtr.',
                'Sq. Ft.',
                'Acres',
                'Bigha',
                'Hectare',
            ];

            DB::table('project_units')->insert(array_map(
                fn (string $name, int $index) => [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                $units,
                array_keys($units)
            ));
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('properties', 'project_area_value')) {
            Schema::table('properties', function (Blueprint $table): void {
                $table->dropColumn(['project_area_value', 'project_area_unit']);
            });
        }

        Schema::dropIfExists('project_units');
        Schema::dropIfExists('property_configurations');
    }
};
