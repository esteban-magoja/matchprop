<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Wave\Facades\Wave;
use App\Http\Controllers\PropertySearchController;
use App\Http\Controllers\PropertyController;

// Property routes
Route::get('/search-properties', [PropertySearchController::class, 'index'])->name('property.search');
Route::get('/property/{id}', [PropertyController::class, 'show'])->name('property.show');

// Wave routes
Wave::routes();