<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('property_listing_units', function (Blueprint $table) {
            $table->unsignedBigInteger('price')->nullable()->after('available_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_listing_units', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
