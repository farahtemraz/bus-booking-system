<?php

use App\Http\Controllers\TripController;

Route::get('/', function () {
    return view('welcome');
})->middleware('auth');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/trips', [TripController::class, 'index'])->name('trips.index')->middleware('auth');
Route::get('/trips/{id}', [TripController::class, 'show'])->name('trips.show')->middleware('auth');
Route::post('/trips', [TripController::class, 'find'])->name('trips.find')->middleware('auth');
Route::post('/trips/book/{id}', [TripController::class, 'book'])->name('trips.book')->middleware('auth');

