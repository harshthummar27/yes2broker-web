<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_promo_items', function (Blueprint $table) {
            $table->string('placement')->default('home')->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('home_promo_items', function (Blueprint $table) {
            $table->dropColumn('placement');
        });
    }
};
