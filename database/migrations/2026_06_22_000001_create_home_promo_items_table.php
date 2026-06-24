<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_promo_items', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // banner | property
            $table->string('banner_image')->nullable();
            $table->unsignedInteger('property_id')->nullable()->index();
            $table->string('slogan')->nullable();
            $table->string('link_url')->nullable();
            $table->string('button_text')->default('Explore More');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_promo_items');
    }
};
