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

// Property search route
Route::get('/search-properties', [PropertySearchController::class, 'index'])->name('property.search');

// Wave routes
Wave::routes();