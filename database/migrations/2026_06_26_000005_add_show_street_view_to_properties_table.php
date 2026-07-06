<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('properties', 'show_street_view')) {
            Schema::table('properties', function (Blueprint $table): void {
                $table->boolean('show_street_view')->default(true)->after('street_view_embed_url');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('properties', 'show_street_view')) {
            Schema::table('properties', function (Blueprint $table): void {
                $table->dropColumn('show_street_view');
            });
        }
    }
};
