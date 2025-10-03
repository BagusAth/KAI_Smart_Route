<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::post('/routes/recommendations', function () {
	return redirect()->route('home')->with('status', 'Fitur rekomendasi rute akan segera tersedia.');
})->name('routes.recommend');
