<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RouteRecommendationController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::post('/routes/recommendations', RouteRecommendationController::class)->name('routes.recommend');
