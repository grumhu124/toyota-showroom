<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('car_categories')->restrictOnDelete();
            $table->string('code', 50)->unique();
            $table->string('slug', 220)->unique();
            $table->string('name', 200);
            $table->unsignedSmallInteger('year');
            $table->string('color', 100);
            $table->json('specs')->nullable();
            $table->decimal('price', 15, 0);
            $table->string('status', 20)->default('available')->index();
            $table->boolean('is_visible')->default(false);
            $table->index(['is_visible', 'status', 'price']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
