<x-layouts.app>
    <x-app.container class="lg:space-y-6">
        
        <x-app.heading
            title="Detalle de Solicitud"
            description="Propiedades que coinciden con tu búsqueda"
            :border="false"
        >
            <x-slot name="actions">
                <a href="{{ route('dashboard.requests.edit', $propertyRequest) }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    Editar
                </a>
                <a href="{{ route('dashboard.requests.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                    Volver
                </a>
            </x-slot>
        </x-app.heading>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Request Details Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $propertyRequest->title }}</h2>
                    <div class="flex items-center gap-2">
                        @if($propertyRequest->is_active)
                            @if($propertyRequest->isExpired())
                                <span class="px-3 py-1 text-sm font-medium bg-red-100 text-red-800 rounded-full">
                                    Expirada
                                </span>
                            @else
                                <span class="px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full">
                                    Activa
                                </span>
                            @endif
                        @else
                            <span class="px-3 py-1 text-sm font-medium bg-gray-100 text-gray-800 rounded-full">
                                Inactiva
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <p class="text-gray-700 mb-6">{{ $propertyRequest->description }}</p>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 pb-4 border-b border-gray-200">
                <div>
                    <span class="text-sm text-gray-500">Tipo de Propiedad</span>
                    <p class="font-medium text-gray-900 capitalize">{{ $propertyRequest->property_type }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Operación</span>
                    <p class="font-medium text-gray-900 capitalize">{{ $propertyRequest->transaction_type }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Presupuesto</span>
                    <p class="font-medium text-gray-900">{{ $propertyRequest->budget_range }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500">Ubicación</span>
                    <p class="font-medium text-gray-900">{{ $propertyRequest->city ?? $propertyRequest->state ?? $propertyRequest->country }}</p>
                </div>
            </div>

            @if($propertyRequest->min_bedrooms || $propertyRequest->min_bathrooms || $propertyRequest->min_parking_spaces || $propertyRequest->min_area)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if($propertyRequest->min_bedrooms)
                        <div>
                            <span class="text-sm text-gray-500">Habitaciones mín.</span>
                            <p class="font-medium text-gray-900">{{ $propertyRequest->min_bedrooms }}</p>
                        </div>
                    @endif
                    @if($propertyRequest->min_bathrooms)
                        <div>
                            <span class="text-sm text-gray-500">Baños mín.</span>
                            <p class="font-medium text-gray-900">{{ $propertyRequest->min_bathrooms }}</p>
                        </div>
                    @endif
                    @if($propertyRequest->min_parking_spaces)
                        <div>
                            <span class="text-sm text-gray-500">Cocheras mín.</span>
                            <p class="font-medium text-gray-900">{{ $propertyRequest->min_parking_spaces }}</p>
                        </div>
                    @endif
                    @if($propertyRequest->min_area)
                        <div>
                            <span class="text-sm text-gray-500">Área mín.</span>
                            <p class="font-medium text-gray-900">{{ $propertyRequest->min_area }}m²</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Matches Section -->
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">
                Propiedades Coincidentes ({{ $matches->count() }})
            </h3>

            @if($matches->isEmpty())
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay coincidencias aún</h3>
                    <p class="text-gray-600">Revisa tu solicitud más tarde, las propiedades se actualizan constantemente.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($matches as $listing)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Match Level Badge -->
                            <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                                @if($listing->match_level === 'exact')
                                    <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        ✓ Match Exacto
                                    </span>
                                @elseif($listing->match_level === 'semantic')
                                    <span class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        ⚡ Match Inteligente
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                        ~ Match Flexible
                                    </span>
                                @endif
                                <span class="text-xs text-gray-500">{{ $listing->match_score }}% coincidencia</span>
                            </div>

                            <!-- Property Image -->
                            <div class="relative h-48 bg-gray-200">
                                @if($listing->primaryImage)
                                    <img src="{{ $listing->primaryImage->image_url }}" 
                                         alt="{{ $listing->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Property Details -->
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-900 mb-2 line-clamp-1">{{ $listing->title }}</h4>
                                <p class="text-lg font-bold text-blue-600 mb-2">
                                    {{ $listing->currency }} {{ number_format($listing->price, 0) }}
                                </p>
                                <p class="text-sm text-gray-600 mb-3">
                                    {{ $listing->city }}, {{ $listing->state }}
                                </p>

                                <!-- Property Features -->
                                <div class="flex gap-3 text-sm text-gray-600 mb-4">
                                    @if($listing->bedrooms)
                                        <span>{{ $listing->bedrooms }} hab.</span>
                                    @endif
                                    @if($listing->bathrooms)
                                        <span>{{ $listing->bathrooms }} baños</span>
                                    @endif
                                    @if($listing->area)
                                        <span>{{ $listing->area }}m²</span>
                                    @endif
                                </div>

                                <!-- Match Details -->
                                @if(!empty($listing->match_details))
                                    <div class="mb-4 p-2 bg-gray-50 rounded text-xs text-gray-600">
                                        <strong>Coincide en:</strong>
                                        <ul class="list-disc list-inside mt-1">
                                            @foreach(array_slice($listing->match_details, 0, 3) as $detail)
                                                <li>{{ $detail }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- View Button -->
                                <a href="{{ route('property.show', $listing->id) }}" 
                                   class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </x-app.container>
</x-layouts.app>
