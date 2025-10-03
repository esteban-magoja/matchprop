<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyListing;

class PropertyController extends Controller
{
    /**
     * Display the specified property listing.
     */
    public function show($id)
    {
        $property = PropertyListing::with(['user', 'images'])
            ->where('is_active', true)
            ->findOrFail($id);

        // Get related properties (same city or same property type)
        $relatedProperties = PropertyListing::with(['primaryImage'])
            ->where('is_active', true)
            ->where('id', '!=', $property->id)
            ->where(function($query) use ($property) {
                $query->where('city', $property->city)
                      ->orWhere('property_type', $property->property_type);
            })
            ->where('country', $property->country)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // SEO data
        $seo = (object) [
            'title' => $property->title . ' - ' . ucfirst($property->transaction_type) . ' en ' . $property->city,
            'description' => $this->generateMetaDescription($property),
            'image' => $property->primaryImage?->image_url ?? ($property->images->first()?->image_url ?? asset('images/default-property.jpg')),
            'type' => 'article',
            'image_w' => 1200,
            'image_h' => 630,
        ];

        return view('property-detail', compact('property', 'relatedProperties', 'seo'));
    }

    /**
     * Generate SEO-friendly meta description for the property.
     */
    private function generateMetaDescription($property)
    {
        $parts = [];
        
        // Tipo de propiedad y transacción
        $parts[] = ucfirst($property->property_type) . ' en ' . $property->transaction_type;
        
        // Ubicación
        $parts[] = $property->city . ', ' . $property->state . ', ' . $property->country;
        
        // Precio
        $parts[] = $property->currency . ' ' . number_format($property->price);
        
        // Características principales
        $features = [];
        if ($property->bedrooms) {
            $features[] = $property->bedrooms . ' hab.';
        }
        if ($property->bathrooms) {
            $features[] = $property->bathrooms . ' baños';
        }
        if ($property->area) {
            $features[] = number_format($property->area) . 'm²';
        }
        if ($property->parking_spaces) {
            $features[] = $property->parking_spaces . ' cochera' . ($property->parking_spaces > 1 ? 's' : '');
        }
        
        if (!empty($features)) {
            $parts[] = implode(', ', $features);
        }
        
        // Unir todas las partes
        $description = implode(' • ', $parts);
        
        // Limitar a 160 caracteres para SEO
        if (strlen($description) > 160) {
            $description = substr($description, 0, 157) . '...';
        }
        
        return $description;
    }
}
