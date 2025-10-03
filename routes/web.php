<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RouteRecommendationController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/routes/recommendations', RouteRecommendationController::class)->name('routes.recommend.show');
Route::post('/routes/recommendations', RouteRecommendationController::class)->name('routes.recommend');

Route::view('/reservasi', 'reservasi')->name('reservasi');
