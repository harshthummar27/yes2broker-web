<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->date('possession_date')->nullable()->after('possession');
            $table->unsignedBigInteger('price_min_amount')->nullable()->after('price');
            $table->unsignedBigInteger('price_max_amount')->nullable()->after('price_min_amount');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->dropColumn(['possession_date', 'price_min_amount', 'price_max_amount']);
        });
    }
};
