<x-layouts.app>
    <x-app.container class="lg:space-y-6">
        
        <x-app.heading
            title="Editar Solicitud"
            description="Actualiza los detalles de tu solicitud"
            :border="false"
        />

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('dashboard.requests.update', $propertyRequest) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Título de la solicitud *
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $propertyRequest->title) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción detallada *
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="5"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              required>{{ old('description', $propertyRequest->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="property_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Propiedad *
                        </label>
                        <select name="property_type" 
                                id="property_type" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="casa" {{ old('property_type', $propertyRequest->property_type) == 'casa' ? 'selected' : '' }}>Casa</option>
                            <option value="departamento" {{ old('property_type', $propertyRequest->property_type) == 'departamento' ? 'selected' : '' }}>Departamento</option>
                            <option value="local" {{ old('property_type', $propertyRequest->property_type) == 'local' ? 'selected' : '' }}>Local Comercial</option>
                            <option value="oficina" {{ old('property_type', $propertyRequest->property_type) == 'oficina' ? 'selected' : '' }}>Oficina</option>
                            <option value="terreno" {{ old('property_type', $propertyRequest->property_type) == 'terreno' ? 'selected' : '' }}>Terreno</option>
                            <option value="campo" {{ old('property_type', $propertyRequest->property_type) == 'campo' ? 'selected' : '' }}>Campo</option>
                            <option value="galpon" {{ old('property_type', $propertyRequest->property_type) == 'galpon' ? 'selected' : '' }}>Galpón</option>
                        </select>
                        @error('property_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Operación *
                        </label>
                        <select name="transaction_type" 
                                id="transaction_type" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="venta" {{ old('transaction_type', $propertyRequest->transaction_type) == 'venta' ? 'selected' : '' }}>Venta</option>
                            <option value="alquiler" {{ old('transaction_type', $propertyRequest->transaction_type) == 'alquiler' ? 'selected' : '' }}>Alquiler</option>
                        </select>
                        @error('transaction_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                            Moneda *
                        </label>
                        <select name="currency" 
                                id="currency" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="USD" {{ old('currency', $propertyRequest->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="ARS" {{ old('currency', $propertyRequest->currency) == 'ARS' ? 'selected' : '' }}>ARS</option>
                            <option value="EUR" {{ old('currency', $propertyRequest->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="min_budget" class="block text-sm font-medium text-gray-700 mb-2">
                            Presupuesto Mínimo
                        </label>
                        <input type="number" 
                               name="min_budget" 
                               id="min_budget" 
                               value="{{ old('min_budget', $propertyRequest->min_budget) }}"
                               step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('min_budget')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_budget" class="block text-sm font-medium text-gray-700 mb-2">
                            Presupuesto Máximo *
                        </label>
                        <input type="number" 
                               name="max_budget" 
                               id="max_budget" 
                               value="{{ old('max_budget', $propertyRequest->max_budget) }}"
                               step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('max_budget')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                            País *
                        </label>
                        <input type="text" 
                               name="country" 
                               id="country" 
                               value="{{ old('country', $propertyRequest->country) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                            Provincia/Estado
                        </label>
                        <input type="text" 
                               name="state" 
                               id="state" 
                               value="{{ old('state', $propertyRequest->state) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                            Ciudad
                        </label>
                        <input type="text" 
                               name="city" 
                               id="city" 
                               value="{{ old('city', $propertyRequest->city) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Características Mínimas</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <label for="min_bedrooms" class="block text-sm font-medium text-gray-700 mb-2">
                                Habitaciones
                            </label>
                            <input type="number" 
                                   name="min_bedrooms" 
                                   id="min_bedrooms" 
                                   value="{{ old('min_bedrooms', $propertyRequest->min_bedrooms) }}"
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="min_bathrooms" class="block text-sm font-medium text-gray-700 mb-2">
                                Baños
                            </label>
                            <input type="number" 
                                   name="min_bathrooms" 
                                   id="min_bathrooms" 
                                   value="{{ old('min_bathrooms', $propertyRequest->min_bathrooms) }}"
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="min_parking_spaces" class="block text-sm font-medium text-gray-700 mb-2">
                                Cocheras
                            </label>
                            <input type="number" 
                                   name="min_parking_spaces" 
                                   id="min_parking_spaces" 
                                   value="{{ old('min_parking_spaces', $propertyRequest->min_parking_spaces) }}"
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="min_area" class="block text-sm font-medium text-gray-700 mb-2">
                                Área (m²)
                            </label>
                            <input type="number" 
                                   name="min_area" 
                                   id="min_area" 
                                   value="{{ old('min_area', $propertyRequest->min_area) }}"
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Expiración
                    </label>
                    <input type="date" 
                           name="expires_at" 
                           id="expires_at" 
                           value="{{ old('expires_at', $propertyRequest->expires_at?->format('Y-m-d')) }}"
                           min="{{ now()->addDay()->format('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('expires_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', $propertyRequest->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">
                        Solicitud activa
                    </label>
                </div>

                <div class="flex gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-150">
                        Guardar Cambios
                    </button>
                    <a href="{{ route('dashboard.requests.show', $propertyRequest) }}" 
                       class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors duration-150">
                        Cancelar
                    </a>
                    <button type="button"
                            onclick="if(confirm('¿Estás seguro de que deseas eliminar esta solicitud?')) { document.getElementById('delete-form').submit(); }"
                            class="ml-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-150">
                        Eliminar
                    </button>
                </div>
            </form>

            <!-- Delete Form -->
            <form id="delete-form" 
                  action="{{ route('dashboard.requests.destroy', $propertyRequest) }}" 
                  method="POST" 
                  class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>

    </x-app.container>
</x-layouts.app>
