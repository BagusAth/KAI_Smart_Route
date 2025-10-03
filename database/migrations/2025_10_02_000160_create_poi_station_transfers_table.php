<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poi_station_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('point_of_interest_id')->constrained('points_of_interest')->cascadeOnDelete();
            $table->foreignId('station_id')->constrained('stations')->cascadeOnDelete();
            $table->enum('mode', ['Walk', 'Shuttle', 'Taxi', 'Bus', 'Commuter', 'RideHailing']);
            $table->unsignedSmallInteger('duration_minutes');
            $table->decimal('distance_km', 6, 2)->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['point_of_interest_id', 'station_id', 'mode'], 'poi_station_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poi_station_transfers');
    }
};
