<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->text('map_embed_url')->nullable()->change();
            $table->text('street_view_embed_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('map_embed_url')->nullable()->change();
            $table->string('street_view_embed_url')->nullable()->change();
        });
    }
};
