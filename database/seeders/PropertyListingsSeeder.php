<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PropertyListing;
use App\Models\User;

class PropertyListingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos usuarios existentes
        $users = User::limit(5)->get();
        
        if ($users->isEmpty()) {
            echo "No hay usuarios en la base de datos. Por favor, cree algunos usuarios primero.\n";
            return;
        }
        
        // Datos de ejemplo para anuncios inmobiliarios
        $properties = [
            [
                'title' => 'Hermosa casa en el centro',
                'description' => 'Amplia casa de 3 dormitorios con jardín y garaje. Ubicada en una zona tranquila pero cercana al centro de la ciudad.',
                'property_type' => 'casa',
                'transaction_type' => 'venta',
                'price' => 250000,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'parking_spaces' => 2,
                'area' => 150,
                'address' => 'Calle Principal 123',
                'city' => 'Ciudad Ejemplo',
                'state' => 'Estado Ejemplo',
                'country' => 'País Ejemplo',
                'postal_code' => '12345',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Moderno departamento en zona comercial',
                'description' => 'Departamento de 2 dormitorios con vista panorámica. Edificio con gimnasio y piscina.',
                'property_type' => 'departamento',
                'transaction_type' => 'alquiler',
                'price' => 1200,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'parking_spaces' => 1,
                'area' => 80,
                'address' => 'Avenida Comercial 456',
                'city' => 'Ciudad Ejemplo',
                'state' => 'Estado Ejemplo',
                'country' => 'País Ejemplo',
                'postal_code' => '12346',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Terreno ideal para desarrollo',
                'description' => 'Terreno plano de 500 m² en zona residencial en crecimiento. Ideal para construir vivienda o proyecto comercial.',
                'property_type' => 'terreno',
                'transaction_type' => 'venta',
                'price' => 75000,
                'bedrooms' => null,
                'bathrooms' => null,
                'parking_spaces' => null,
                'area' => 500,
                'address' => 'Camino Rural S/N',
                'city' => 'Ciudad Ejemplo',
                'state' => 'Estado Ejemplo',
                'country' => 'País Ejemplo',
                'postal_code' => '12347',
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Local comercial en zona peatonal',
                'description' => 'Local comercial de 60 m² en exclusiva zona peatonal. Excelente ubicación para todo tipo de negocios.',
                'property_type' => 'local',
                'transaction_type' => 'alquiler',
                'price' => 800,
                'bedrooms' => null,
                'bathrooms' => 1,
                'parking_spaces' => null,
                'area' => 60,
                'address' => 'Calle Peatonal 789',
                'city' => 'Ciudad Ejemplo',
                'state' => 'Estado Ejemplo',
                'country' => 'País Ejemplo',
                'postal_code' => '12348',
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Acogedora casa de campo',
                'description' => 'Encantadora casa de campo de 4 dormitorios con amplio jardín y piscina. Perfecta para escapadas semanales o residencia permanente.',
                'property_type' => 'casa',
                'transaction_type' => 'venta',
                'price' => 320000,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'parking_spaces' => 3,
                'area' => 200,
                'address' => 'Ruta Campestre Km 10',
                'city' => 'Ciudad Ejemplo',
                'state' => 'Estado Ejemplo',
                'country' => 'País Ejemplo',
                'postal_code' => '12349',
                'is_featured' => false,
                'is_active' => true,
            ],
        ];
        
        // Crear los anuncios asignándolos a usuarios aleatorios
        foreach ($properties as $propertyData) {
            $user = $users->random();
            $propertyData['user_id'] = $user->id;
            
            PropertyListing::create($propertyData);
        }
        
        echo "Se han creado " . count($properties) . " anuncios de ejemplo.\n";
    }
}
