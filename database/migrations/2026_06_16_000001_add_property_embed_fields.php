<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('street_view_embed_url')->nullable()->after('map_embed_url');
            $table->string('brochure_url')->nullable()->after('street_view_embed_url');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['street_view_embed_url', 'brochure_url']);
        });
    }
};
