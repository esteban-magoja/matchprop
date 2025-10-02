<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyListing;
use Pgvector\Laravel\Vector;

class PropertySearchController extends Controller
{
    public function index(Request $request)
    {
        $startTime = microtime(true);
        
        $searchTerm = trim($request->get('search', ''));
        $selectedCountry = $request->get('country', '');
        
        // Check if this is a search request (has search parameters)
        $isSearchRequest = $request->has('search') || $request->has('country');
        
        // Validation - only validate if user is actually searching
        $validationErrors = [];
        $hasValidSearch = false;
        
        if ($isSearchRequest) {
            // Both country and search term are required when searching
            if (empty($selectedCountry)) {
                $validationErrors[] = 'Debes seleccionar un país.';
            }
            
            if (empty($searchTerm)) {
                $validationErrors[] = 'Debes escribir un término de búsqueda.';
            } elseif (strlen($searchTerm) < 5) {
                $validationErrors[] = 'El término de búsqueda debe tener al menos 5 caracteres.';
            }
            
            // Only proceed if both validations pass
            if (empty($validationErrors)) {
                $hasValidSearch = true;
            }
        }
        
        // Get available countries
        $countries = PropertyListing::distinct('country')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->where('is_active', true)
            ->pluck('country')
            ->sort()
            ->values()
            ->toArray();

        $properties = collect();

        if ($hasValidSearch && empty($validationErrors)) {
            $properties = $this->performSearch($searchTerm, $selectedCountry);
        }

        $searchTime = round((microtime(true) - $startTime) * 1000); // Convert to milliseconds

        return view('property-search', [
            'properties' => $properties,
            'searchTerm' => $searchTerm,
            'selectedCountry' => $selectedCountry,
            'countries' => $countries,
            'totalResults' => $properties->count(),
            'searchTime' => $searchTime,
            'validationErrors' => $validationErrors,
            'hasValidSearch' => $hasValidSearch,
            'isSearchRequest' => $isSearchRequest
        ]);
    }

    private function performSearch(string $searchTerm, string $selectedCountry)
    {
        try {
            $query = PropertyListing::query()
                ->with(['primaryImage'])
                ->where('is_active', true);

            // Filter by country if selected
            if (!empty($selectedCountry)) {
                $query->where('country', $selectedCountry);
            }

            // If there's a search term, use embedding search
            if (!empty($searchTerm)) {
                \Log::info("Starting embedding search for: " . $searchTerm);
                
                $client = \OpenAI::client(config('openai.api_key'));
                $model = config('openai.embeddings_model');

                $response = $client->embeddings()->create([
                    'model' => $model,
                    'input' => $searchTerm,
                ]);

                $embedding = new Vector($response->embeddings[0]->embedding);
                
                \Log::info("Embedding created successfully");

                $query = $query
                    ->select('*')
                    ->selectRaw('(1 - (embedding <=> ?)) * 100 as similarity', [$embedding])
                    ->whereRaw('(embedding <=> ?) < 0.7', [$embedding]) // More permissive threshold
                    ->orderByDesc('similarity');
                    
                \Log::info("Query built successfully");
            } else {
                $query = $query->orderByDesc('is_featured')->orderByDesc('created_at');
            }

            $results = $query->limit(20)->get();
            \Log::info("Found " . $results->count() . " results");
            
            return $results;

        } catch (\Exception $e) {
            \Log::error('Property search error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Fallback to simple search if embedding search fails
            if (!empty($searchTerm)) {
                return PropertyListing::where('is_active', true)
                    ->where(function($query) use ($searchTerm) {
                        $query->where('title', 'like', '%'.$searchTerm.'%')
                              ->orWhere('description', 'like', '%'.$searchTerm.'%')
                              ->orWhere('city', 'like', '%'.$searchTerm.'%');
                    })
                    ->when(!empty($selectedCountry), function($query) use ($selectedCountry) {
                        return $query->where('country', $selectedCountry);
                    })
                    ->with(['primaryImage'])
                    ->limit(20)
                    ->get();
            }
            
            return collect();
        }
    }
}
