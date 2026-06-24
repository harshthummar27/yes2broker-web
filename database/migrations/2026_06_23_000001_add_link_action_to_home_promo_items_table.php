<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_promo_items', function (Blueprint $table) {
            $table->string('link_action')->default('url')->after('banner_image');
            $table->string('form_title')->nullable()->after('link_action');
        });
    }

    public function down(): void
    {
        Schema::table('home_promo_items', function (Blueprint $table) {
            $table->dropColumn(['link_action', 'form_title']);
        });
    }
};
