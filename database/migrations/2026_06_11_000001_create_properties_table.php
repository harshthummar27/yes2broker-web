<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('location');
            $table->string('bhk');
            $table->string('area');
            $table->string('possession');
            $table->string('price');
            $table->decimal('price_min_lakhs', 12, 2)->default(0);
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->longText('description')->nullable();
            $table->json('overview')->nullable();
            $table->json('amenities')->nullable();
            $table->json('faqs')->nullable();
            $table->string('map_embed_url')->nullable();
            $table->string('city')->nullable();
            $table->string('property_type')->nullable();
            $table->boolean('is_new')->default(true);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
