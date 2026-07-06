<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('localities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['city_id', 'slug']);
        });

        Schema::table('properties', function (Blueprint $table): void {
            $table->string('address_line_1')->nullable()->after('location');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('locality')->nullable()->after('address_line_2');
            $table->string('state')->nullable()->after('city');
        });

        $now = now();

        DB::table('states')->insert([
            [
                'name' => 'Gujarat',
                'slug' => 'gujarat',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $ahmedabadId = DB::table('cities')->where('slug', 'ahmedabad')->value('id');
        $gandhinagarId = DB::table('cities')->where('slug', 'gandhinagar')->value('id');

        $ahmedabadLocalities = [
            'Ambali',
            'Ambawadi',
            'Bhadaj',
            'Bopal',
            'Chandkheda',
            'Gota',
            'Hebatpur',
            'Khoraj',
            'Motera',
            'Nava Vadaj',
            'Science City',
            'Shela',
            'Shilaj',
            'South Bopal',
            'Thaltej',
            'Vaishno Devi Circle',
            'Zundal',
        ];

        $gandhinagarLocalities = [
            'Kudasan',
            'Raysan',
            'Sargasan',
        ];

        $localityRows = [];

        foreach ($ahmedabadLocalities as $index => $name) {
            if ($ahmedabadId === null) {
                break;
            }

            $localityRows[] = [
                'city_id' => $ahmedabadId,
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($gandhinagarLocalities as $index => $name) {
            if ($gandhinagarId === null) {
                break;
            }

            $localityRows[] = [
                'city_id' => $gandhinagarId,
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'sort_order' => $index + 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($localityRows !== []) {
            DB::table('localities')->insert($localityRows);
        }
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->dropColumn(['address_line_1', 'address_line_2', 'locality', 'state']);
        });

        Schema::dropIfExists('localities');
        Schema::dropIfExists('states');
    }
};
