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
use App\Http\Controllers\PropertyMessageController;
use App\Http\Controllers\RequestSearchController;
use App\Http\Controllers\TermsController;
use Illuminate\Support\Facades\Route;

// Redirect de raíz al locale por defecto
Route::get('/', function () {
    $locale = session('locale', config('locales.default', 'es'));
    return redirect("/{$locale}");
});

// Rutas con prefijo de locale
Route::prefix('{locale}')->where(['locale' => 'es|en'])->group(function () {
    
    // Home route (temporal, hasta que Wave/Folio se actualice)
    Route::get('/', function () {
        $seo = [
            'title' => setting('site.title', 'Raxta - Plataforma Inmobiliaria Inteligente'),
            'description' => setting('site.description', 'Conectamos propiedades con compradores y agentes de forma inteligente.'),
            'image' => url('/og_image.png'),
            'type' => 'website'
        ];
        return view('theme::pages.index', compact('seo'));
    })->name('home');
    
    // Dashboard route (Folio no soporta prefijo de locale dinámico)
    Route::get('/dashboard', function () {
        // Importar las clases necesarias
        $userListings = \App\Models\PropertyListing::where('user_id', auth()->id())->active()->count();
        $userRequests = \App\Models\PropertyRequest::where('user_id', auth()->id())->active()->count();
        $unreadMessages = \App\Models\PropertyMessage::whereHas('propertyListing', function($query) {
            $query->where('user_id', auth()->id());
        })->where('is_read', false)->count();
        
        // Obtener algunos matches recientes
        $matchingService = app(\App\Services\PropertyMatchingService::class);
        $recentListings = \App\Models\PropertyListing::where('user_id', auth()->id())->active()->take(3)->get();
        $totalMatches = 0;
        foreach ($recentListings as $listing) {
            $totalMatches += $matchingService->findMatchesForListing($listing, 5)->count();
        }
        
        return view('theme::pages.dashboard.index', compact('userListings', 'userRequests', 'unreadMessages', 'totalMatches'));
    })->name('dashboard')->middleware('auth');
    
    // Terms acceptance route (only POST, GET is handled by Folio)
    Route::post('/dashboard/terms/accept', [TermsController::class, 'accept'])->name('terms.accept')->middleware('auth');

    // Property routes
    Route::get('/search-properties', [PropertySearchController::class, 'index'])->name('property.search');
    Route::get('/property/{id}', [PropertyController::class, 'show'])->name('property.show');
    Route::post('/property/{id}/message', [PropertyController::class, 'sendMessage'])->name('property.message')->middleware('auth');

    // Request Search routes (Public)
    Route::get('/search-requests', [RequestSearchController::class, 'index'])->name('requests.search');

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

        // Property Message routes (Dashboard)
        Route::prefix('dashboard/messages')->name('dashboard.messages.')->group(function () {
            Route::get('/', [PropertyMessageController::class, 'index'])->name('index');
            Route::get('/{id}', [PropertyMessageController::class, 'show'])->name('show');
            Route::post('/{id}/mark-read', [PropertyMessageController::class, 'markAsRead'])->name('mark-read');
            Route::post('/{id}/mark-unread', [PropertyMessageController::class, 'markAsUnread'])->name('mark-unread');
            Route::delete('/{id}', [PropertyMessageController::class, 'destroy'])->name('destroy');
        });
    });
});

// Wave routes (sin prefijo de locale, ya que Wave maneja sus propias rutas)
Wave::routes();