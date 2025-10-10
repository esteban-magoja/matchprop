<?php
    use function Laravel\Folio\{middleware, name};
	use App\Models\PropertyListing;
	use App\Models\PropertyRequest;
	use App\Models\PropertyMessage;
	use App\Services\PropertyMatchingService;
	
	middleware('auth');
    name('dashboard');

	$userListings = PropertyListing::where('user_id', auth()->id())->active()->count();
	$userRequests = PropertyRequest::where('user_id', auth()->id())->active()->count();
	$unreadMessages = PropertyMessage::whereHas('propertyListing', function($query) {
		$query->where('user_id', auth()->id());
	})->where('is_read', false)->count();
	
	// Obtener algunos matches recientes
	$matchingService = app(PropertyMatchingService::class);
	$recentListings = PropertyListing::where('user_id', auth()->id())->active()->take(3)->get();
	$totalMatches = 0;
	foreach ($recentListings as $listing) {
		$totalMatches += $matchingService->findMatchesForListing($listing, 5)->count();
	}
?>

<x-layouts.app>
	<x-app.container x-data class="lg:space-y-6" x-cloak>
        
		<x-app.alert id="dashboard_alert" class="hidden lg:flex">Panel de control donde puedes gestionar tus anuncios, solicitudes y ver coincidencias.</x-app.alert>

        <x-app.heading
                title="Dashboard"
                description="Gestiona tus propiedades y solicitudes"
                :border="false"
            />

		<!-- Quick Stats -->
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
			<div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-sm text-gray-600 mb-1">Mis Anuncios</p>
						<p class="text-3xl font-bold text-gray-900">{{ $userListings }}</p>
					</div>
					<svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
					</svg>
				</div>
				<div class="mt-3 text-blue-600">
					<a href="/property-listings" class="mt-4 text-sm text-blue-600 hover:text-blue-700 font-medium">Ver anuncios</a> | 
					<a href="/property-listings/create" class="mt-4 text-sm text-blue-600 hover:text-blue-700 font-medium">Publicar anuncio</a>
				</div>
			</div>

			<div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-sm text-gray-600 mb-1">Mis Solicitudes</p>
						<p class="text-3xl font-bold text-gray-900">{{ $userRequests }}</p>
					</div>
					<svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
					</svg>
				</div>
				<div class="mt-3 text-green-600">
					<a href="{{ route('dashboard.requests.index') }}" class="mt-4 text-sm text-green-600 hover:text-green-700 font-medium">Ver solicitudes</a> | 
					<a href="{{ route('dashboard.requests.create') }}" class="mt-4 text-sm text-green-600 hover:text-green-700 font-medium">Agregar solicitud</a>
				</div>
			</div>

			<div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-sm text-gray-600 mb-1">Mensajes</p>
						<p class="text-3xl font-bold text-gray-900">{{ $unreadMessages }}</p>
					</div>
					<svg class="w-12 h-12 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
					</svg>
				</div>
				@if($unreadMessages > 0)
					<div class="mt-3 text-orange-600">
						<a href="{{ route('dashboard.messages.index') }}" class="mt-4 text-sm text-orange-600 hover:text-orange-700 font-medium">{{ $unreadMessages }} sin leer</a>
					</div>
				@else
					<div class="mt-3 text-orange-600">
						<a href="{{ route('dashboard.messages.index') }}" class="mt-4 text-sm text-orange-600 hover:text-orange-700 font-medium">Ver mensajes</a>
					</div>
				@endif
			</div>

			<div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-sm text-gray-600 mb-1">Matches Encontrados</p>
						<p class="text-3xl font-bold text-gray-900">{{ $totalMatches }}</p>
					</div>
					<svg class="w-12 h-12 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
					</svg>
				</div>
				<div class="mt-3 text-purple-600">
					<a href="{{ route('dashboard.matches.index') }}" class="mt-4 text-sm text-purple-600 hover:text-purple-700 font-medium">Ver matches</a>
				</div>	
			</div>
		</div>

       

		

		<div class="mt-5 space-y-5">
			@subscriber
				<p>You are a subscribed user with the <strong>{{ auth()->user()->roles()->first()->name }}</strong> role. Learn <a href="https://devdojo.com/wave/docs/features/roles-permissions" target="_blank" class="underline">more about roles</a> here.</p>
				<x-app.message-for-subscriber />
			@else
				<p>This current logged in user has a <strong>{{ auth()->user()->roles()->first()->name }}</strong> role. To upgrade, <a href="{{ route('settings.subscription') }}" class="underline">subscribe to a plan</a>. Learn <a href="https://devdojo.com/wave/docs/features/roles-permissions" target="_blank" class="underline">more about roles</a> here.</p>
			@endsubscriber
			
			@admin
				<x-app.message-for-admin />
			@endadmin
		</div>
    </x-app.container>
</x-layouts.app>
