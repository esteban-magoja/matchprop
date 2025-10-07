<?php

namespace App\Http\Controllers;

use App\Models\PropertyRequest;
use App\Services\PropertyMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PropertyRequestController extends Controller
{
    protected $matchingService;

    public function __construct(PropertyMatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * Display a listing of user's property requests.
     */
    public function index()
    {
        $requests = PropertyRequest::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('theme::pages.dashboard.requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new property request.
     */
    public function create()
    {
        $countries = \Nnjeim\World\Models\Country::all();
        $currencies = ['USD', 'ARS', 'EUR', 'BRL', 'MXN', 'CLP'];
        
        return view('theme::pages.dashboard.requests.create', compact('countries', 'currencies'));
    }

    /**
     * Store a newly created property request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'property_type' => 'required|string',
            'transaction_type' => 'required|string',
            'max_budget' => 'required|numeric|min:0',
            'min_budget' => 'nullable|numeric|min:0|lt:max_budget',
            'currency' => 'required|string|max:10',
            'min_bedrooms' => 'nullable|integer|min:0',
            'min_bathrooms' => 'nullable|integer|min:0',
            'min_parking_spaces' => 'nullable|integer|min:0',
            'min_area' => 'nullable|integer|min:0',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $validated['user_id'] = auth()->id();

        // Generar embedding
        $embedding = $this->generateEmbedding($validated['title'], $validated['description']);
        if ($embedding) {
            $validated['embedding'] = $embedding;
        }

        $propertyRequest = PropertyRequest::create($validated);

        return redirect()
            ->route('dashboard.requests.show', $propertyRequest)
            ->with('success', 'Solicitud creada exitosamente');
    }

    /**
     * Display the specified property request with matches.
     */
    public function show(PropertyRequest $propertyRequest)
    {
        // Verificar que el usuario sea el dueño
        if ($propertyRequest->user_id !== auth()->id()) {
            abort(403);
        }

        // Obtener matches
        $matches = $this->matchingService->findMatchesForRequest($propertyRequest, 20);

        return view('theme::pages.dashboard.requests.show', compact('propertyRequest', 'matches'));
    }

    /**
     * Show the form for editing the specified property request.
     */
    public function edit(PropertyRequest $propertyRequest)
    {
        // Verificar que el usuario sea el dueño
        if ($propertyRequest->user_id !== auth()->id()) {
            abort(403);
        }

        return view('theme::pages.dashboard.requests.edit', compact('propertyRequest'));
    }

    /**
     * Update the specified property request.
     */
    public function update(Request $request, PropertyRequest $propertyRequest)
    {
        // Verificar que el usuario sea el dueño
        if ($propertyRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'property_type' => 'required|string',
            'transaction_type' => 'required|string',
            'max_budget' => 'required|numeric|min:0',
            'min_budget' => 'nullable|numeric|min:0|lt:max_budget',
            'currency' => 'required|string|max:10',
            'min_bedrooms' => 'nullable|integer|min:0',
            'min_bathrooms' => 'nullable|integer|min:0',
            'min_parking_spaces' => 'nullable|integer|min:0',
            'min_area' => 'nullable|integer|min:0',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:today',
            'is_active' => 'boolean',
        ]);

        // Regenerar embedding si cambió la descripción
        if ($propertyRequest->description !== $validated['description'] || 
            $propertyRequest->title !== $validated['title']) {
            $embedding = $this->generateEmbedding($validated['title'], $validated['description']);
            if ($embedding) {
                $validated['embedding'] = $embedding;
            }
        }

        $propertyRequest->update($validated);

        return redirect()
            ->route('dashboard.requests.show', $propertyRequest)
            ->with('success', 'Solicitud actualizada exitosamente');
    }

    /**
     * Remove the specified property request.
     */
    public function destroy(PropertyRequest $propertyRequest)
    {
        // Verificar que el usuario sea el dueño
        if ($propertyRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $propertyRequest->delete();

        return redirect()
            ->route('dashboard.requests.index')
            ->with('success', 'Solicitud eliminada exitosamente');
    }

    /**
     * Toggle active status of the property request.
     */
    public function toggleActive(PropertyRequest $propertyRequest)
    {
        // Verificar que el usuario sea el dueño
        if ($propertyRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $propertyRequest->update([
            'is_active' => !$propertyRequest->is_active
        ]);

        $status = $propertyRequest->is_active ? 'activada' : 'desactivada';

        return back()->with('success', "Solicitud {$status} exitosamente");
    }

    /**
     * Generate embedding using OpenAI API.
     *
     * @param string $title
     * @param string $description
     * @return array|null
     */
    protected function generateEmbedding(string $title, string $description): ?array
    {
        try {
            $text = $title . ' ' . $description;
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/embeddings', [
                'input' => $text,
                'model' => 'text-embedding-ada-002',
            ]);

            if ($response->successful()) {
                return $response->json('data.0.embedding');
            }

            \Log::error('Error generating embedding: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            \Log::error('Exception generating embedding: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get states for a country (AJAX)
     */
    public function getStates(Request $request)
    {
        try {
            $countryId = $request->country_id;
            
            if (!$countryId) {
                return response()->json(['error' => 'country_id is required'], 400);
            }
            
            $states = \Nnjeim\World\Models\State::where('country_id', $countryId)
                ->orderBy('name')
                ->get(['id', 'name', 'country_id'])
                ->toArray();
            
            return response()->json($states, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('Error getting states: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading states'], 500);
        }
    }

    /**
     * Get cities for a state (AJAX)
     */
    public function getCities(Request $request)
    {
        try {
            $stateId = $request->state_id;
            
            if (!$stateId) {
                return response()->json(['error' => 'state_id is required'], 400);
            }
            
            $cities = \Nnjeim\World\Models\City::where('state_id', $stateId)
                ->orderBy('name')
                ->get(['id', 'name', 'state_id'])
                ->toArray();
            
            return response()->json($cities, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            \Log::error('Error getting cities: ' . $e->getMessage());
            return response()->json(['error' => 'Error loading cities'], 500);
        }
    }
}
