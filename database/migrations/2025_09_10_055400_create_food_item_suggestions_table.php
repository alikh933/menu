<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_item_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_item_id')->constrained('food_items')->cascadeOnDelete();
            $table->unsignedBigInteger('suggested_item_id');
            $table->string('suggested_name');
            $table->unsignedInteger('suggested_price');
            $table->string('suggested_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_item_suggestions');
    }
};


