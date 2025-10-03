<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('train_carriages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('train_id')->constrained('trains')->cascadeOnDelete();
            $table->unsignedTinyInteger('car_number');
            $table->string('carriage_type')->default('Seating');
            $table->string('class')->nullable();
            $table->json('layout')->nullable();
            $table->json('amenities')->nullable();
            $table->timestamps();

            $table->unique(['train_id', 'car_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('train_carriages');
    }
};
