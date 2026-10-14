<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->foreignId('customer_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('inquiry_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('car_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('service_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('customer_car_info', 200)->nullable();
            $table->string('contact_name', 100);
            $table->string('contact_phone', 20);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('requested_at');
            $table->dateTime('confirmed_at')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('note')->nullable();
            $table->string('cancel_reason', 500)->nullable();
            $table->index(['assigned_to', 'status', 'confirmed_at']);
            $table->index(['customer_id', 'status']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
