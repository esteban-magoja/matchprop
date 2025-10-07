<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use App\Models\PropertyRequest;
use Livewire\Attributes\Rule;
use Nnjeim\World\Models\Country;
use Nnjeim\World\Models\State;
use Nnjeim\World\Models\City;
use Livewire\Attributes\Computed;

middleware('auth');
name('dashboard.requests.create-livewire');

new class extends Component {
    
    #[Rule('required|string|max:255')]
    public string $title = '';

    #[Rule('required|string|min:20')]
    public string $description = '';

    #[Rule('required|string')]
    public string $property_type = 'casa';

    #[Rule('required|string')]
    public string $transaction_type = 'venta';

    #[Rule('nullable|numeric|min:0')]
    public ?string $min_budget = '';

    #[Rule('required|numeric|min:0')]
    public string $max_budget = '';

    #[Rule('required|string|max:3')]
    public string $currency = 'USD';

    #[Rule('nullable|integer|min:0')]
    public ?int $min_bedrooms = null;

    #[Rule('nullable|integer|min:0')]
    public ?int $min_bathrooms = null;

    #[Rule('nullable|integer|min:0')]
    public ?int $min_parking_spaces = null;

    #[Rule('nullable|integer|min:0')]
    public ?int $min_area = null;

    #[Rule('nullable|string|max:255')]
    public ?string $city = '';

    #[Rule('required')]
    public $selectedCountry = null;

    #[Rule('nullable')]
    public $selectedState = null;

    #[Rule('nullable|date|after:today')]
    public $expires_at = null;

    public $countries;
    public $states = [];
    public $cities = [];
    public $availableCurrencies = ['USD', 'ARS', 'EUR', 'BRL', 'MXN', 'CLP', 'COP'];

    #[Computed]
    public function country()
    {
        return Country::find($this->selectedCountry)?->name;
    }

    #[Computed]
    public function state()
    {
        return State::find($this->selectedState)?->name;
    }

    public function mount()
    {
        $this->countries = Country::all();
    }

    public function updatedSelectedCountry($countryId)
    {
        $this->states = State::where('country_id', $countryId)->orderBy('name')->get();
        $this->selectedState = null;
        $this->cities = [];
        $this->city = '';

        // Actualizar moneda según el país
        $country = Country::find($countryId);
        if ($country && isset($country->currency['code'])) {
            $this->currency = $country->currency['code'];
        }
    }

    public function updatedSelectedState($stateId)
    {
        if ($stateId) {
            $this->cities = City::where('state_id', $stateId)->orderBy('name')->get();
        } else {
            $this->cities = [];
        }
        $this->city = '';
    }

    public function save(): void
    {
        $validated = $this->validate();
        
        $validated['user_id'] = auth()->id();
        $validated['country'] = $this->country;
        $validated['state'] = $this->state;

        // Generar embedding
        $embedding = $this->generateEmbedding($validated['title'], $validated['description']);
        if ($embedding) {
            $validated['embedding'] = $embedding;
        }

        $propertyRequest = PropertyRequest::create($validated);

        $this->redirectRoute('dashboard.requests.show', $propertyRequest);
    }

    protected function generateEmbedding(string $title, string $description): ?array
    {
        try {
            $text = $title . ' ' . $description;
            
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/embeddings', [
                'input' => $text,
                'model' => 'text-embedding-ada-002',
            ]);

            if ($response->successful()) {
                return $response->json('data.0.embedding');
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Exception generating embedding: ' . $e->getMessage());
            return null;
        }
    }
};
?>

<x-layouts.app>
    @volt('property-request-create')
    <x-app.container>
        <x-app.heading
            title="Nueva Solicitud"
            description="Describe la propiedad que estás buscando"
            :border="false"
        />

        <div class="mt-6">
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Título de la solicitud *
                            </label>
                            <input type="text" 
                                   wire:model="title" 
                                   id="title" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Ej: Busco casa con jardín en Córdoba">
                            @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción detallada *
                            </label>
                            <textarea wire:model="description" 
                                      id="description" 
                                      rows="5"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Describe con detalle qué tipo de propiedad estás buscando..."></textarea>
                            <p class="mt-1 text-sm text-gray-500">Mínimo 20 caracteres</p>
                            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="property_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Propiedad *
                                </label>
                                <select wire:model="property_type" 
                                        id="property_type" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="casa">Casa</option>
                                    <option value="departamento">Departamento</option>
                                    <option value="local">Local Comercial</option>
                                    <option value="oficina">Oficina</option>
                                    <option value="terreno">Terreno</option>
                                    <option value="campo">Campo</option>
                                    <option value="galpon">Galpón</option>
                                </select>
                            </div>

                            <div>
                                <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Operación *
                                </label>
                                <select wire:model="transaction_type" 
                                        id="transaction_type" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="venta">Venta</option>
                                    <option value="alquiler">Alquiler</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Presupuesto</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                Moneda *
                            </label>
                            <select wire:model="currency" 
                                    id="currency" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($availableCurrencies as $curr)
                                    <option value="{{ $curr }}">{{ $curr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="min_budget" class="block text-sm font-medium text-gray-700 mb-2">
                                Presupuesto Mínimo
                            </label>
                            <input type="number" 
                                   wire:model="min_budget" 
                                   id="min_budget" 
                                   step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0">
                            @error('min_budget') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="max_budget" class="block text-sm font-medium text-gray-700 mb-2">
                                Presupuesto Máximo *
                            </label>
                            <input type="number" 
                                   wire:model="max_budget" 
                                   id="max_budget" 
                                   step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="250000">
                            @error('max_budget') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ubicación</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                País *
                            </label>
                            <select wire:model.live="selectedCountry" 
                                    id="country" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccionar país...</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedCountry') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                Provincia/Estado
                            </label>
                            <select wire:model.live="selectedState" 
                                    id="state" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    {{ empty($states) ? 'disabled' : '' }}>
                                <option value="">Seleccionar provincia...</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                Ciudad
                            </label>
                            <select wire:model="city" 
                                    id="city" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    {{ empty($cities) ? 'disabled' : '' }}>
                                <option value="">Seleccionar ciudad...</option>
                                @foreach($cities as $cityItem)
                                    <option value="{{ $cityItem->name }}">{{ $cityItem->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Características Mínimas (Opcional)</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label for="min_bedrooms" class="block text-sm font-medium text-gray-700 mb-2">
                                Habitaciones
                            </label>
                            <input type="number" 
                                   wire:model="min_bedrooms" 
                                   id="min_bedrooms" 
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="min_bathrooms" class="block text-sm font-medium text-gray-700 mb-2">
                                Baños
                            </label>
                            <input type="number" 
                                   wire:model="min_bathrooms" 
                                   id="min_bathrooms" 
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="min_parking_spaces" class="block text-sm font-medium text-gray-700 mb-2">
                                Cocheras
                            </label>
                            <input type="number" 
                                   wire:model="min_parking_spaces" 
                                   id="min_parking_spaces" 
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="min_area" class="block text-sm font-medium text-gray-700 mb-2">
                                Área (m²)
                            </label>
                            <input type="number" 
                                   wire:model="min_area" 
                                   id="min_area" 
                                   min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Expiración (Opcional)
                        </label>
                        <input type="date" 
                               wire:model="expires_at" 
                               id="expires_at" 
                               min="{{ now()->addDay()->format('Y-m-d') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Si no especificas una fecha, la solicitud permanecerá activa indefinidamente.</p>
                        @error('expires_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-4 pt-6">
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-150 disabled:opacity-75 disabled:cursor-wait">
                        <span wire:loading.remove wire:target="save">Crear Solicitud</span>
                        <span wire:loading wire:target="save">Guardando...</span>
                    </button>
                    <a href="{{ route('dashboard.requests.index') }}" 
                       class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors duration-150">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </x-app.container>
    @endvolt
</x-layouts.app>
