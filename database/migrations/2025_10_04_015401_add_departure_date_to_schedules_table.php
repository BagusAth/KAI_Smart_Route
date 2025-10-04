<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->date('departure_date')->after('train_id')->nullable();
            
            // Tambahkan index untuk performa query
            $table->index(['train_id', 'station_id', 'departure_date']);
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex(['train_id', 'station_id', 'departure_date']);
            $table->dropColumn('departure_date');
        });
    }
};