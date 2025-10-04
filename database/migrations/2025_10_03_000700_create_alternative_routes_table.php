<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alternative_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_station_id')->constrained('stations')->cascadeOnDelete();
            $table->foreignId('destination_station_id')->constrained('stations')->cascadeOnDelete();
            $table->foreignId('primary_train_id')->nullable()->constrained('trains')->nullOnDelete();
            $table->foreignId('via_station_id')->nullable()->constrained('stations')->nullOnDelete();
            $table->foreignId('alternative_train_id')->nullable()->constrained('trains')->nullOnDelete();
            $table->enum('transfer_type', ['same-platform', 'cross-platform', 'exit-reenter', 'feeder', 'shuttle', 'walk'])->nullable();
            $table->unsignedSmallInteger('total_duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // The corrected code with a shorter, custom index name
            $table->index(
                ['origin_station_id', 'destination_station_id'],
                'alt_routes_origin_dest_idx' // A much shorter name
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alternative_routes');
    }
};
