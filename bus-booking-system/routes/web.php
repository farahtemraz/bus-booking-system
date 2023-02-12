<?php

use App\Http\Controllers\TripController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/trips', [TripController::class, 'index'])->name('trips.index')->middleware('auth');
Route::get('/trips/{id}', [TripController::class, 'show'])->name('trips.show')->middleware('auth');
Route::get('/trips/create', [TripController::class, 'create'])->name('trips.create');
Route::post('/trips', [TripController::class, 'find'])->name('trips.find');
Route::post('/trips/book/{id}', [TripController::class, 'book'])->name('trips.book');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
