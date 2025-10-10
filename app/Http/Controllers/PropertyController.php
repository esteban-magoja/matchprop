<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyListing;
use App\Models\PropertyMessage;
use App\Mail\PropertyMessageReceived;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Store a contact message for a property listing.
     */
    public function sendMessage(Request $request, $id)
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para enviar un mensaje.');
        }

        // Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:2000',
        ]);

        // Buscar la propiedad
        $property = PropertyListing::with('user')->findOrFail($id);

        // Verificar que el usuario no esté contactando su propia propiedad
        if ($property->user_id === Auth::id()) {
            return back()->with('error', 'No puedes enviar un mensaje a tu propia propiedad.');
        }

        // Crear el mensaje
        $propertyMessage = PropertyMessage::create([
            'property_listing_id' => $property->id,
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'message' => $validated['message'],
        ]);

        // Enviar email al propietario
        try {
            Mail::to($property->user->email)->send(new PropertyMessageReceived($propertyMessage));
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Log::error('Failed to send property message email: ' . $e->getMessage());
        }

        return back()->with('success', '¡Tu mensaje ha sido enviado! El anunciante se pondrá en contacto contigo pronto.');
    }
}
