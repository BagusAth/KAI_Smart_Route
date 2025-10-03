<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_a_id')->constrained('stations')->cascadeOnDelete();
            $table->foreignId('station_b_id')->constrained('stations')->cascadeOnDelete();
            $table->string('line_name');
            $table->enum('transport_mode', ['Intercity', 'Commuter', 'Shuttle', 'Taxi', 'Bus', 'LRT'])->default('Intercity');
            $table->decimal('distance_km', 6, 2)->nullable();
            $table->unsignedSmallInteger('average_duration_minutes')->nullable();
            $table->decimal('base_fare', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
