<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('train_id')->constrained('trains')->cascadeOnDelete();
            $table->foreignId('station_id')->constrained('stations')->cascadeOnDelete();
            $table->foreignId('platform_id')->nullable()->constrained('platforms')->nullOnDelete();
            $table->unsignedInteger('stop_order');
            $table->unsignedSmallInteger('arrival_offset_minutes')->nullable();
            $table->unsignedSmallInteger('departure_offset_minutes')->nullable();
            $table->unsignedSmallInteger('stop_duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['train_id', 'station_id']);
            $table->unique(['train_id', 'stop_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
