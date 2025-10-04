<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Hapus unique constraint lama
            $table->dropUnique(['train_id', 'station_id']);
            
            // Buat unique constraint baru yang include departure_date
            $table->unique(['train_id', 'station_id', 'departure_date'], 'schedules_train_station_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Kembalikan ke constraint lama
            $table->dropUnique('schedules_train_station_date_unique');
            $table->unique(['train_id', 'station_id']);
        });
    }
};