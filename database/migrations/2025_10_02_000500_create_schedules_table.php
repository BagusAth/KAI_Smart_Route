<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('train_id')->constrained('trains')->cascadeOnDelete();
            $table->foreignId('station_id')->constrained('stations')->cascadeOnDelete();
            $table->foreignId('platform_id')->nullable()->constrained('platforms')->nullOnDelete();
            $table->time('arrival')->nullable();
            $table->time('departure')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedSmallInteger('available_seats')->nullable();
            $table->enum('status', ['Scheduled', 'Delayed', 'Cancelled'])->default('Scheduled');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['train_id', 'station_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
