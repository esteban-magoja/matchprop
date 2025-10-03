# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Wave is a Laravel-based SaaS framework that provides essential features for building subscription-based applications. The application uses a modular architecture with themes, plugins, and a custom admin panel built with Filament.

## Customizaciones Implementadas

### Campos de Usuario Adicionales (Octubre 2025)
- **Migración**: `2025_10_01_174705_add_additional_fields_to_users_table.php`
- **Campos agregados**: agency, movil, address, city, state, country (todos nullable)
- **Formulario de registro**: `/signup` con campo móvil opcional
- **Formulario de perfil**: `/settings/profile` con todos los campos (móvil requerido)
- **Redirecciones**: `/register` y `/auth/register` → `/signup`

### Configuración de Perfil
- **Archivo**: `config/profile.php`
- **Campo eliminado**: "What do you do for a living?" (occupation)
- **Campo 'About'**: Cambiado de requerido a opcional

### Estructura de Datos
- **Campos directos**: Guardados en tabla `users`
- **Campos dinámicos**: Guardados en `profile_key_values` via config
- **Remember token**: Configurado correctamente en registro personalizado

### Sistema de Propiedades Inmobiliarias (Diciembre 2025)

#### Modelos y Tablas
- **PropertyListing**: Modelo principal de anuncios inmobiliarios
  - Tabla: `property_listings`
  - Relaciones: `user`, `images`, `primaryImage`
  - Scopes: `active()`, `featured()`
  - Usa pgvector para embeddings de búsqueda semántica
  
- **PropertyImage**: Imágenes de propiedades
  - Tabla: `property_images`
  - Relación: `propertyListing`
  - Campo `is_primary` para imagen destacada

#### Controladores
- **PropertySearchController**: Búsqueda de propiedades con IA
  - Ruta: `/search-properties` → `property.search`
  - Búsqueda semántica usando OpenAI embeddings (pgvector)
  - Filtrado por país (obligatorio)
  - Validación: mínimo 5 caracteres en búsqueda
  
- **PropertyController**: Detalle de propiedades
  - Ruta: `/property/{id}` → `property.show`
  - Vista: `property-detail.blade.php`
  - SEO dinámico (title, description, Open Graph)
  - Propiedades relacionadas (mismo tipo o ciudad)

#### Vistas y Características

**Página de Búsqueda** (`property-search.blade.php`):
- Búsqueda inteligente con embeddings de OpenAI
- Filtro obligatorio por país
- Resultados con score de similitud
- Cards responsivas con imagen, precio, ubicación
- Botón "Ver Detalles" enlaza a ficha individual

**Página de Detalle** (`property-detail.blade.php`):
- Layout: `<x-layouts.marketing :seo="$seo">`
- Galería de imágenes con navegación (flechas, teclado)
- Estadísticas principales con iconos (horizontal layout):
  - Habitaciones (icono cama)
  - Baños (icono ducha)
  - m² Cubiertos (icono dimensiones)
  - Cocheras (icono auto)
  - m² Terreno (icono ubicación)
- Mapa interactivo OpenStreetMap + Leaflet.js:
  - Solo si tiene coordenadas (latitude/longitude)
  - Marcador personalizado (pin con emoji 🏠)
  - Círculo de área (100m radio)
  - Sin popup (marcador visual simple)
  - Centrado automático con `invalidateSize()`
- Sidebar de contacto:
  - Info del anunciante (avatar, nombre, agencia, email)
  - Botón WhatsApp (verde oscuro #128C7E)
  - Solo si user tiene campo `movil`
  - Formulario de contacto
  - Botón "Llamar Ahora" (solo con móvil)
- Sección "Compartir" (Facebook, Twitter, Copiar)
- Propiedades relacionadas (4 similares)

#### SEO Optimización
Cada propiedad genera automáticamente:

**Title Tag**:
```
{título} - {transacción} en {ciudad}
Ejemplo: Casa moderna - Venta en Córdoba
```

**Meta Description** (límite 160 caracteres):
```
{tipo} en {transacción} • {ubicación} • {precio} • {características}
Ejemplo: Casa en venta • Córdoba, Argentina • USD 250,000 • 3 hab., 2 baños, 150m²
```

**Open Graph Tags**:
- og:title, og:description, og:image
- og:type: "article"
- Dimensiones imagen: 1200x630px
- Imagen: primaryImage → primera imagen → fallback

**Método**: `PropertyController::generateMetaDescription()`
- Construye descripción dinámica con datos de la propiedad
- Prioriza: tipo, ubicación, precio, características
- Trunca a 160 caracteres si excede

#### Integración OpenStreetMap
- **Librería**: Leaflet.js v1.9.4
- **Tiles**: OpenStreetMap (gratuito, sin API key)
- **CDN**: unpkg.com/leaflet@1.9.4
- **Características**:
  - Mapa responsive (h-80, 320px)
  - Zoom inicial: nivel 15
  - Marcador custom con pin azul y emoji casa
  - Control de escala métrico
  - Enlace a OpenStreetMap
  - Recalcula tamaño con `invalidateSize()` (fix centrado)

#### Notas Importantes
- **Blade Components**: Pasar variables a layouts con `:variable="$value"`
  - Ejemplo: `<x-layouts.marketing :seo="$seo">`
- **Embeddings**: Usa OpenAI API para búsqueda semántica
- **Validación búsqueda**: País obligatorio + mínimo 5 caracteres
- **Cache**: Limpiar vistas después de cambios (`php artisan view:clear`)
- **Iconos**: SVG outline style para mejor claridad visual

## Development Commands

### Frontend Development
- `npm run dev` - Start Vite development server
- `npm run build` - Build assets for production

### Backend Development
- `php artisan serve` - Start Laravel development server
- `composer run dev` - Start full development environment (server, queue, logs, and Vite)

### Database & Migrations
- `php artisan migrate` - Run database migrations
- `php artisan db:seed` - Seed the database
- `php artisan migrate:fresh --seed` - Fresh migration with seeding

### Testing
- `php artisan test` - Run PHPUnit tests
- `vendor/bin/pest` - Run Pest tests

### Queue Management
- `php artisan queue:work` - Process queued jobs
- `php artisan queue:listen --tries=1` - Listen for jobs with retry limit

### Wave-Specific Commands
- `php artisan wave:cancel-expired-subscriptions` - Cancel expired subscriptions
- `php artisan wave:create-plugin` - Create a new plugin

## Architecture Overview

### Core Structure
- `app/` - Standard Laravel application files
- `wave/` - Wave framework core files and components
- `resources/themes/` - Theme files (Blade templates, assets)
- `resources/plugins/` - Plugin system files
- `config/wave.php` - Main Wave configuration

### Key Components

#### Wave Service Provider (`wave/src/WaveServiceProvider.php`)
- Registers middleware, Livewire components, and Blade directives
- Handles plugin registration and theme management
- Configures Filament colors and authentication

#### Models & Database
- User model extends Wave User with subscription capabilities
- Subscription management with Stripe/Paddle integration
- Role-based permissions using Spatie Laravel Permission

#### Theme System
- Multiple themes available in `resources/themes/`
- Theme switching in demo mode via cookies
- Folio integration for page routing

#### Admin Panel
- Filament-based admin interface
- Resource management for users, posts, plans, etc.
- Located in `app/Filament/`

### Billing Integration
- Supports both Stripe and Paddle
- Configured via `config/wave.php` and environment variables
- Webhook handling for subscription events

### Plugin System
- Plugins located in `resources/plugins/`
- Auto-loading via `PluginServiceProvider`
- Plugin creation command available

## Configuration

### Environment Variables
- `WAVE_DOCS` - Show/hide documentation
- `WAVE_DEMO` - Enable demo mode
- `WAVE_BAR` - Show development bar
- `BILLING_PROVIDER` - Set to 'stripe' or 'paddle'

### Important Config Files
- `config/wave.php` - Main Wave configuration
- `config/themes.php` - Theme configuration
- `config/settings.php` - Application settings

## Testing

The application uses Pest for testing with PHPUnit as the underlying framework. Test files are located in `tests/` with separate directories for Feature and Unit tests.

## Development Notes

- The application uses Laravel Folio for page routing
- Livewire components handle dynamic UI interactions
- Filament provides the admin interface
- Theme development follows Blade templating conventions
- Plugin development follows Laravel package conventions

## Performance Optimizations

### Caching Strategy
- User subscription/admin status cached for 5-10 minutes
- Active plans cached for 30 minutes
- Categories cached for 1 hour
- Helper files cached permanently until cleared
- Theme colors cached for 1 hour
- Plugin lists cached for 1 hour

### Cache Clearing
- User caches cleared via `$user->clearUserCache()` method
- Plan caches cleared via `Plan::clearCache()` method
- Category caches cleared via `Category::clearCache()` method

### Database Optimizations
- Eager loading relationships to prevent N+1 queries
- Cached query results for frequently accessed data
- Optimized middleware to use cached user roles

### Usage Tips
- Use `Plan::getActivePlans()` instead of `Plan::where('active', 1)->get()`
- Use `Plan::getByName($name)` instead of `Plan::where('name', $name)->first()`
- Use `Category::getAllCached()` instead of `Category::all()`
- Always clear relevant caches when updating user roles, plans, or categories

### Installation & CI Compatibility
- All caching methods include fallbacks for when cache service is unavailable
- Service provider guards against cache binding issues during package discovery
- Compatible with automated testing environments and CI/CD pipelines