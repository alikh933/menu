<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_item_side_dish', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_item_id')->constrained('food_items')->cascadeOnDelete();
            $table->foreignId('side_dish_id')->constrained('side_dishes')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['food_item_id', 'side_dish_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_item_side_dish');
    }
};


