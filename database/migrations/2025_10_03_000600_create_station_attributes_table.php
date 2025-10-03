<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->unique()->constrained('stations')->cascadeOnDelete();
            $table->string('zone')->nullable();
            $table->boolean('is_interchange')->default(false);
            $table->json('facilities')->nullable();
            $table->string('opening_hours')->nullable();
            $table->json('accessibility')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_attributes');
    }
};
