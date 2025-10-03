<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_of_interest', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('Landmark');
            $table->string('city');
            $table->string('island')->default('Jawa');
            $table->foreignId('default_station_id')->nullable()->constrained('stations')->nullOnDelete();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE points_of_interest ADD COLUMN location POINT NOT NULL SRID 4326');
    }

    public function down(): void
    {
        Schema::dropIfExists('points_of_interest');
    }
};
