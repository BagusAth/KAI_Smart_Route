<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RouteRecommendationController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/routes/recommendations', RouteRecommendationController::class)->name('routes.recommend.show');
Route::post('/routes/recommendations', RouteRecommendationController::class)->name('routes.recommend');

Route::get('/reservasi', [ReservationController::class, 'show'])->name('reservasi.show');
Route::post('/reservasi', [ReservationController::class, 'submit'])->name('reservasi.submit');
Route::get('/reservasi/konfirmasi', [ReservationController::class, 'confirm'])->name('reservasi.confirm');
Route::post('/reservasi/kursi', [ReservationController::class, 'storeSeats'])->name('reservasi.seats');
Route::get('/reservasi/pembayaran', [ReservationController::class, 'payment'])->name('reservasi.payment');
