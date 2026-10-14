<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('car_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('contact_name', 100);
            $table->string('contact_phone', 20);
            $table->string('contact_email')->nullable();
            $table->text('content');
            $table->string('source', 20)->default('form');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('status', 20)->default('new');
            $table->unsignedInteger('access_version')->default(1);
            $table->text('internal_note')->nullable();
            $table->string('close_result', 50)->nullable();
            $table->index(['assigned_to', 'status']);
            $table->index(['customer_id', 'car_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
