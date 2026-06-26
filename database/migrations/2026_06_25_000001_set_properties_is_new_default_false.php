<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('properties')->update(['is_new' => false]);

        Schema::table('properties', function (Blueprint $table): void {
            $table->boolean('is_new')->default(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->boolean('is_new')->default(true)->change();
        });
    }
};
