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
use App\Http\Controllers\PropertyRequestController;
use App\Http\Controllers\PropertyMatchController;

// Property routes
Route::get('/search-properties', [PropertySearchController::class, 'index'])->name('property.search');
Route::get('/property/{id}', [PropertyController::class, 'show'])->name('property.show');

// Property Request routes (Dashboard)
Route::middleware('auth')->group(function () {
    Route::prefix('dashboard/requests')->name('dashboard.requests.')->group(function () {
        Route::get('/', [PropertyRequestController::class, 'index'])->name('index');
        Route::get('/create', [PropertyRequestController::class, 'create'])->name('create');
        Route::post('/', [PropertyRequestController::class, 'store'])->name('store');
        Route::get('/{propertyRequest}', [PropertyRequestController::class, 'show'])->name('show');
        Route::get('/{propertyRequest}/edit', [PropertyRequestController::class, 'edit'])->name('edit');
        Route::put('/{propertyRequest}', [PropertyRequestController::class, 'update'])->name('update');
        Route::delete('/{propertyRequest}', [PropertyRequestController::class, 'destroy'])->name('destroy');
        Route::post('/{propertyRequest}/toggle-active', [PropertyRequestController::class, 'toggleActive'])->name('toggle-active');
    });

    // AJAX routes for locations
    Route::get('/api/states', [PropertyRequestController::class, 'getStates'])->name('api.states');
    Route::get('/api/cities', [PropertyRequestController::class, 'getCities'])->name('api.cities');

    // Property Match routes (Dashboard)
    Route::prefix('dashboard/matches')->name('dashboard.matches.')->group(function () {
        Route::get('/', [PropertyMatchController::class, 'index'])->name('index');
        Route::get('/listing/{listing}', [PropertyMatchController::class, 'show'])->name('show');
    });
});

// Wave routes
Wave::routes();