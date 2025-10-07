<x-layouts.app>
    <x-app.container class="lg:space-y-6">
        
        <x-app.heading
            title="Mis Solicitudes"
            description="Gestiona tus solicitudes de búsqueda de propiedades"
            :border="false"
        >
            <x-slot name="actions">
                <a href="{{ route('dashboard.requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Solicitud
                </a>
            </x-slot>
        </x-app.heading>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($requests->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes solicitudes</h3>
                <p class="text-gray-600 mb-4">Crea tu primera solicitud para encontrar la propiedad ideal</p>
                <a href="{{ route('dashboard.requests.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Crear Solicitud
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($requests as $request)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="text-xl font-semibold text-gray-900">{{ $request->title }}</h3>
                                    
                                    @if($request->is_active)
                                        @if($request->isExpired())
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                                Expirada
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                Activa
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                            Inactiva
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-gray-600 line-clamp-2">{{ $request->description }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <span class="text-sm text-gray-500">Tipo</span>
                                <p class="font-medium text-gray-900 capitalize">{{ $request->property_type }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Operación</span>
                                <p class="font-medium text-gray-900 capitalize">{{ $request->transaction_type }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Presupuesto</span>
                                <p class="font-medium text-gray-900">{{ $request->budget_range }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-500">Ubicación</span>
                                <p class="font-medium text-gray-900">{{ $request->city ?? $request->state ?? $request->country }}</p>
                            </div>
                        </div>

                        @if($request->min_bedrooms || $request->min_bathrooms || $request->min_area)
                            <div class="flex gap-4 mb-4 text-sm text-gray-600">
                                @if($request->min_bedrooms)
                                    <span>Min. {{ $request->min_bedrooms }} hab.</span>
                                @endif
                                @if($request->min_bathrooms)
                                    <span>Min. {{ $request->min_bathrooms }} baños</span>
                                @endif
                                @if($request->min_area)
                                    <span>Min. {{ $request->min_area }}m²</span>
                                @endif
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-500">
                                Creada {{ $request->created_at->diffForHumans() }}
                                @if($request->expires_at)
                                    • Expira {{ $request->expires_at->format('d/m/Y') }}
                                @endif
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('dashboard.requests.show', $request) }}" 
                                   class="px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                    Ver Matches
                                </a>
                                <a href="{{ route('dashboard.requests.edit', $request) }}" 
                                   class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                    Editar
                                </a>
                                <form action="{{ route('dashboard.requests.toggle-active', $request) }}" 
                                      method="POST" 
                                      class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="px-3 py-2 text-sm font-medium {{ $request->is_active ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition-colors">
                                        {{ $request->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        @endif

    </x-app.container>
</x-layouts.app>
