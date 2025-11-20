# Plan de Implementación i18n (Español/Inglés)

**Fecha de inicio:** 2025-10-17  
**Duración estimada:** 10-12 días  
**Estado:** 🔴 NO INICIADO

---

## 📋 Índice Rápido

- [Progreso General](#progreso-general)
- [Plan Diario Detallado](#plan-diario-detallado)
- [Comandos de Inicio Rápido](#comandos-de-inicio-rápido)
- [Arquitectura de Decisiones](#arquitectura-de-decisiones)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Progreso General

| Día | Fase | Estado | Fecha | Notas |
|-----|------|--------|-------|-------|
| 1 | Fundamentos y Configuración | ✅ Completado | 2025-11-19 | Middleware, rutas, helpers, migraciones |
| 2 | Base de Datos y Modelos | ✅ Completado | 2025-11-20 | Trait Translatable, modelos, factory, seeders |
| 3 | Archivos de Traducción | ⏸️ Pendiente | - | - |
| 4 | Controladores - Search & Detail | ⏸️ Pendiente | - | - |
| 5 | Controladores - Dashboard CRUD | ⏸️ Pendiente | - | - |
| 6 | Vistas Blade - Páginas Públicas | ⏸️ Pendiente | - | - |
| 7 | Vistas Blade - Dashboard | ⏸️ Pendiente | - | - |
| 8 | Embeddings y Búsqueda IA | ⏸️ Pendiente | - | - |
| 9 | SEO y Sitemap | ⏸️ Pendiente | - | - |
| 10 | Emails y Notificaciones | ⏸️ Pendiente | - | - |
| 11 | Filament Admin Panel | ⏸️ Pendiente | - | - |
| 12 | Testing y Optimización | ⏸️ Pendiente | - | - |

**Leyenda de Estados:**
- ⏸️ Pendiente
- 🔄 En Progreso
- ✅ Completado
- ⚠️ Bloqueado
- 🔧 Requiere Revisión

---

## 📅 Plan Diario Detallado

### **DÍA 1: Fundamentos y Configuración Base**

**🎯 Objetivo:** Infraestructura i18n lista para usar

**📦 Entregables:**
- [ ] Middleware `SetLocale` creado y registrado
- [ ] Archivo `config/locales.php` creado
- [ ] Rutas con prefijo `{locale}` configuradas en `routes/web.php`
- [ ] Helper functions: `current_locale()`, `route_localized()`, `trans_choice_formatted()`
- [ ] Componente Blade: `language-switcher.blade.php`
- [ ] Migración: campos i18n en `property_listings`
- [ ] Migración: campos i18n en `property_requests`

**📝 Archivos a crear:**
```
app/Http/Middleware/SetLocale.php
config/locales.php
app/helpers.php (o modificar si existe)
resources/themes/anchor/components/language-switcher.blade.php
database/migrations/2025_10_XX_add_i18n_to_property_listings.php
database/migrations/2025_10_XX_add_i18n_to_property_requests.php
```

**📝 Archivos a modificar:**
```
routes/web.php
app/Http/Kernel.php (registrar middleware)
resources/themes/anchor/layouts/app.blade.php (agregar selector)
resources/themes/anchor/layouts/marketing.blade.php (agregar selector)
```

**🧪 Testing:**
```bash
# Verificar que las rutas funcionen con prefijo
curl http://localhost/es/search-properties
curl http://localhost/en/search-properties

# Verificar middleware
php artisan route:list | grep locale

# Ejecutar migraciones
php artisan migrate

# Verificar columnas nuevas
php artisan tinker
>>> Schema::hasColumn('property_listings', 'title_i18n')
```

**📚 Documentación:**
- Leer: Laravel Localization (https://laravel.com/docs/localization)
- Referencia: `CLAUDE.md` sección "Customizaciones Implementadas"

**⏱️ Tiempo estimado:** 4-6 horas

---

### **DÍA 2: Base de Datos y Modelos**

**🎯 Objetivo:** Soporte multiidioma en modelos de Eloquent

**📦 Entregables:**
- [ ] Trait `Translatable` genérico y reutilizable
- [ ] `PropertyListing` con trait Translatable
- [ ] `PropertyRequest` con trait Translatable
- [ ] Métodos: `getTranslation()`, `setTranslation()`, `getAllTranslations()`
- [ ] Seeder: `TranslateExistingPropertiesSeeder`
- [ ] Factory: `PropertyListingFactory` actualizado

**📝 Archivos a crear:**
```
app/Traits/Translatable.php
database/seeders/TranslateExistingPropertiesSeeder.php
```

**📝 Archivos a modificar:**
```
app/Models/PropertyListing.php
app/Models/PropertyRequest.php
database/factories/PropertyListingFactory.php
database/seeders/DatabaseSeeder.php
```

**🧪 Testing:**
```bash
# Limpiar y recrear base de datos
php artisan migrate:fresh --seed

# Verificar datos bilingües
php artisan tinker
>>> $property = PropertyListing::first()
>>> $property->getTranslation('title', 'es')
>>> $property->getTranslation('title', 'en')

# Verificar mutators
>>> $property->setTranslation('title', 'es', 'Nueva Casa')
>>> $property->save()
```

**⏱️ Tiempo estimado:** 4-5 horas

---

### **DÍA 3: Archivos de Traducción (lang/)**

**🎯 Objetivo:** Toda la UI con cadenas traducibles

**📦 Entregables:**
- [ ] `lang/es/properties.php` (tipos, operaciones, características)
- [ ] `lang/en/properties.php`
- [ ] `lang/es/dashboard.php` (menús, títulos, acciones)
- [ ] `lang/en/dashboard.php`
- [ ] `lang/es/messages.php` (mensajes generales, flash)
- [ ] `lang/en/messages.php`
- [ ] `lang/es/validation.php` (reglas custom)
- [ ] `lang/en/validation.php`
- [ ] `lang/es/seo.php` (meta tags, descriptions)
- [ ] `lang/en/seo.php`
- [ ] Enums traducibles en config

**📝 Archivos a crear:**
```
lang/es/properties.php
lang/es/dashboard.php
lang/es/messages.php
lang/es/seo.php
lang/en/properties.php
lang/en/dashboard.php
lang/en/messages.php
lang/en/seo.php
config/property_types.php (opcional)
```

**📝 Archivos a modificar:**
```
lang/es/validation.php
lang/en/validation.php
```

**🧪 Testing:**
```bash
# Verificar traducciones
php artisan tinker
>>> app()->setLocale('es')
>>> __('properties.house')
>>> app()->setLocale('en')
>>> __('properties.house')

# Listar todas las claves
php artisan lang:check (si tienes el paquete)
```

**💡 Tip:** Usa array spread para mantener consistencia:
```php
// lang/es/properties.php
'types' => [
    'house' => 'Casa',
    'apartment' => 'Departamento',
    // ...
]
```

**⏱️ Tiempo estimado:** 3-4 horas

---

### **DÍA 4: Controladores - Parte 1 (Search & Detail)**

**🎯 Objetivo:** Búsqueda y detalle funcionando en ambos idiomas

**📦 Entregables:**
- [ ] `PropertySearchController` actualizado con locale
- [ ] `PropertyController::show()` con traducciones dinámicas
- [ ] SEO tags con hreflang implementados
- [ ] Meta descriptions generadas por idioma
- [ ] Slugs traducidos en URLs
- [ ] Service `SeoService` creado

**📝 Archivos a crear:**
```
app/Services/SeoService.php
app/Http/Requests/PropertySearchRequest.php
```

**📝 Archivos a modificar:**
```
app/Http/Controllers/PropertySearchController.php
app/Http/Controllers/PropertyController.php
routes/web.php (agregar slug a rutas)
```

**🧪 Testing:**
```bash
# Verificar rutas localizadas
php artisan route:list | grep property.show

# Testing manual
curl http://localhost/es/propiedad/1/casa-moderna
curl http://localhost/en/property/1/modern-house

# Verificar SEO tags en HTML
curl -s http://localhost/es/propiedad/1 | grep 'hreflang'
curl -s http://localhost/es/propiedad/1 | grep 'og:locale'
```

**⏱️ Tiempo estimado:** 5-6 horas

---

### **DÍA 5: Controladores - Parte 2 (Dashboard CRUD)**

**🎯 Objetivo:** Gestión completa de anuncios y solicitudes bilingüe

**📦 Entregables:**
- [ ] `PropertyRequestController` actualizado (todos los métodos)
- [ ] Formularios con tabs de idioma (Alpine.js)
- [ ] Validación para campos en ambos idiomas
- [ ] `PropertyMatchController` con traducciones
- [ ] `PropertyMessageController` respetando locale
- [ ] Request classes para validación

**📝 Archivos a crear:**
```
app/Http/Requests/StorePropertyListingRequest.php
app/Http/Requests/UpdatePropertyListingRequest.php
app/Http/Requests/StorePropertyRequestRequest.php
```

**📝 Archivos a modificar:**
```
app/Http/Controllers/PropertyRequestController.php
app/Http/Controllers/PropertyMatchController.php
app/Http/Controllers/PropertyMessageController.php
```

**🧪 Testing:**
```bash
# Testing de creación
php artisan tinker
>>> $data = [
...   'title' => ['es' => 'Casa', 'en' => 'House'],
...   'description' => ['es' => 'Desc ES', 'en' => 'Desc EN'],
... ]
>>> PropertyListing::create($data)

# Verificar validación
# (hacer requests con datos incompletos)
```

**⏱️ Tiempo estimado:** 6-7 horas

---

### **DÍA 6: Vistas Blade - Parte 1 (Páginas Públicas)**

**🎯 Objetivo:** Interfaz pública completamente traducida

**📦 Entregables:**
- [ ] `property-search.blade.php` con `__()`
- [ ] `property-detail.blade.php` con `__()`
- [ ] Componentes: `property-card.blade.php`
- [ ] Layouts con selector de idioma funcional
- [ ] Breadcrumbs traducidos
- [ ] Validaciones de frontend traducidas

**📝 Archivos a modificar:**
```
resources/themes/anchor/pages/search-property-listings/index.blade.php
resources/themes/anchor/pages/property-listings/show.blade.php
resources/themes/anchor/layouts/marketing.blade.php
resources/themes/anchor/components/property-card.blade.php
resources/themes/anchor/components/breadcrumb.blade.php (si existe)
```

**🔍 Buscar y reemplazar:**
```bash
# Identificar strings hardcodeadas
grep -r "Casa\|Departamento\|Buscar" resources/themes/anchor/pages/

# Patrón de reemplazo:
"Casa" → {{ __('properties.types.house') }}
"Buscar" → {{ __('messages.search') }}
```

**🧪 Testing:**
```bash
# Cambiar idioma en navegador
# Verificar que TODO el texto cambie

# Testing de componentes
php artisan view:clear
php artisan serve
# Navegar manualmente
```

**⏱️ Tiempo estimado:** 6-8 horas

---

### **DÍA 7: Vistas Blade - Parte 2 (Dashboard)**

**🎯 Objetivo:** Dashboard interno 100% bilingüe

**📦 Entregables:**
- [ ] `/dashboard/requests/*` todas las vistas
- [ ] `/dashboard/matches/*` todas las vistas
- [ ] `/dashboard/messages/*` todas las vistas
- [ ] `/dashboard/index.blade.php` traducido
- [ ] Formularios con tabs ES/EN (Alpine.js)
- [ ] Botones de acción traducidos
- [ ] Mensajes de confirmación traducidos

**📝 Archivos a modificar:**
```
resources/themes/anchor/pages/dashboard/requests/index.blade.php
resources/themes/anchor/pages/dashboard/requests/create.blade.php
resources/themes/anchor/pages/dashboard/requests/edit.blade.php
resources/themes/anchor/pages/dashboard/requests/show.blade.php
resources/themes/anchor/pages/dashboard/matches/index.blade.php
resources/themes/anchor/pages/dashboard/matches/show.blade.php
resources/themes/anchor/pages/dashboard/messages/index.blade.php
resources/themes/anchor/pages/dashboard/messages/show.blade.php
resources/themes/anchor/pages/dashboard/index.blade.php
```

**💡 Ejemplo de tabs Alpine.js:**
```blade
<div x-data="{ tab: 'es' }">
    <div class="flex border-b">
        <button @click="tab = 'es'" 
                :class="{'border-blue-500 text-blue-600': tab === 'es'}">
            🇪🇸 Español
        </button>
        <button @click="tab = 'en'"
                :class="{'border-blue-500 text-blue-600': tab === 'en'}">
            🇬🇧 English
        </button>
    </div>
    
    <div x-show="tab === 'es'">
        <input name="title[es]" value="{{ old('title.es') }}" required>
    </div>
    <div x-show="tab === 'en'">
        <input name="title[en]" value="{{ old('title.en') }}">
    </div>
</div>
```

**🧪 Testing:**
```bash
# Testing completo del dashboard
# 1. Crear anuncio en ambos idiomas
# 2. Editar anuncio
# 3. Ver matches
# 4. Enviar mensaje
# Todo debe funcionar en ES y EN
```

**⏱️ Tiempo estimado:** 7-9 horas (día más largo)

---

### **DÍA 8: Embeddings y Búsqueda IA Multiidioma**

**🎯 Objetivo:** Búsqueda semántica funcionando en ambos idiomas

**📦 Entregables:**
- [ ] Columnas `embedding_es` y `embedding_en` en BD
- [ ] Observer para auto-generar embeddings al guardar
- [ ] `PropertyMatchingService` actualizado
- [ ] Búsqueda cross-language (buscar en EN encontrar ES)
- [ ] Comando Artisan: `properties:regenerate-embeddings`
- [ ] Service: `EmbeddingService` centralizado

**📝 Archivos a crear:**
```
database/migrations/2025_10_XX_add_multilingual_embeddings.php
app/Observers/PropertyListingObserver.php
app/Console/Commands/RegeneratePropertyEmbeddings.php
app/Services/EmbeddingService.php
```

**📝 Archivos a modificar:**
```
app/Models/PropertyListing.php (registrar observer)
app/Services/PropertyMatchingService.php
app/Providers/AppServiceProvider.php (registrar observer)
```

**🧪 Testing:**
```bash
# Migrar nuevas columnas
php artisan migrate

# Regenerar todos los embeddings
php artisan properties:regenerate-embeddings

# Testing de búsqueda
php artisan tinker
>>> $property = PropertyListing::first()
>>> $property->embedding_es
>>> $property->embedding_en

# Testing cross-language
# Buscar "modern house" debe encontrar "casa moderna"
```

**💡 Performance:**
```php
// Batch processing para embeddings
PropertyListing::chunk(50, function($properties) {
    foreach ($properties as $property) {
        $property->generateEmbeddings();
    }
});
```

**⏱️ Tiempo estimado:** 5-6 horas

---

### **DÍA 9: SEO y Sitemap**

**🎯 Objetivo:** Indexación perfecta en Google

**📦 Entregables:**
- [ ] Tags hreflang automáticos en todas las páginas
- [ ] Canonical URLs correctos por idioma
- [ ] Sitemap multiidioma: `/sitemap-es.xml`, `/sitemap-en.xml`
- [ ] `/sitemap.xml` índice que referencia ambos
- [ ] `robots.txt` actualizado
- [ ] Open Graph tags por idioma
- [ ] Schema.org JSON-LD bilingüe
- [ ] Comando: `sitemaps:generate`

**📝 Archivos a crear:**
```
app/Http/Controllers/SitemapController.php
app/Services/SchemaService.php
app/Console/Commands/GenerateSitemaps.php
resources/views/sitemap/index.blade.php
resources/views/sitemap/properties.blade.php
```

**📝 Archivos a modificar:**
```
routes/web.php (rutas de sitemap)
public/robots.txt
app/Services/SeoService.php (mejorar)
```

**🧪 Testing:**
```bash
# Generar sitemaps
php artisan sitemaps:generate

# Verificar archivos
curl http://localhost/sitemap.xml
curl http://localhost/sitemap-es.xml
curl http://localhost/sitemap-en.xml

# Validar en Google Search Console
# https://search.google.com/test/rich-results
```

**📚 Validación:**
- Google Rich Results Test
- Schema.org Validator
- hreflang Tags Testing Tool

**⏱️ Tiempo estimado:** 4-5 horas

---

### **DÍA 10: Emails y Notificaciones**

**🎯 Objetivo:** Comunicaciones en idioma del usuario

**📦 Entregables:**
- [ ] Campo `locale` agregado a tabla `users`
- [ ] Preferencia de idioma en perfil de usuario
- [ ] Detección automática de idioma en registro
- [ ] Templates de email en ES/EN
- [ ] Notificaciones respetando locale del usuario
- [ ] Mailable classes actualizadas

**📝 Archivos a crear:**
```
database/migrations/2025_10_XX_add_locale_to_users.php
resources/views/emails/es/property-match-found.blade.php
resources/views/emails/en/property-match-found.blade.php
resources/views/emails/es/message-received.blade.php
resources/views/emails/en/message-received.blade.php
app/Mail/PropertyMatchFoundMail.php (si no existe)
app/Notifications/PropertyMatchFoundNotification.php
```

**📝 Archivos a modificar:**
```
app/Models/User.php (agregar campo locale)
resources/themes/anchor/pages/settings/profile.blade.php (agregar selector)
app/Http/Controllers/Auth/RegisterController.php (detectar locale)
```

**🧪 Testing:**
```bash
# Enviar email de prueba
php artisan tinker
>>> $user = User::first()
>>> $user->locale = 'es'
>>> $user->notify(new PropertyMatchFoundNotification($match))

# Verificar en Mailhog/Mailtrap
# Cambiar locale y volver a enviar
>>> $user->locale = 'en'
>>> $user->notify(new PropertyMatchFoundNotification($match))
```

**💡 Detección automática:**
```php
// En RegisterController
$locale = request()->header('Accept-Language');
$locale = substr($locale, 0, 2); // 'es-AR' → 'es'
$user->locale = in_array($locale, ['es', 'en']) ? $locale : 'es';
```

**⏱️ Tiempo estimado:** 4-5 horas

---

### **DÍA 11: Filament Admin Panel**

**🎯 Objetivo:** Panel de administración bilingüe

**📦 Entregables:**
- [ ] Formularios Filament con tabs de idioma
- [ ] Tablas mostrando título en ambos idiomas
- [ ] Traducción de labels y placeholders
- [ ] Filtros traducidos
- [ ] Acciones (actions) traducidas
- [ ] Widgets del dashboard traducidos

**📝 Archivos a crear:**
```
lang/es/filament.php
lang/en/filament.php
app/Filament/Resources/PropertyListingResource/Pages/EditPropertyListing.php
```

**📝 Archivos a modificar:**
```
app/Filament/Resources/PropertyListingResource.php
app/Filament/Resources/PropertyRequestResource.php (si existe)
app/Filament/Widgets/DashboardWidget.php
```

**💡 Ejemplo Filament Tabs:**
```php
use Filament\Forms\Components\Tabs;

Tabs::make('Translations')
    ->tabs([
        Tabs\Tab::make('Español')
            ->schema([
                TextInput::make('title.es')->label('Título'),
                Textarea::make('description.es')->label('Descripción'),
            ]),
        Tabs\Tab::make('English')
            ->schema([
                TextInput::make('title.en')->label('Title'),
                Textarea::make('description.en')->label('Description'),
            ]),
    ])
```

**🧪 Testing:**
```bash
# Acceder al admin panel
# /admin/property-listings/create
# /admin/property-listings/1/edit

# Verificar que:
# 1. Tabs de idioma funcionan
# 2. Se guardan ambas traducciones
# 3. Tabla muestra ambos idiomas
```

**⏱️ Tiempo estimado:** 5-6 horas

---

### **DÍA 12: Testing, Optimización y Deploy**

**🎯 Objetivo:** Sistema listo para producción

**📦 Entregables:**
- [ ] Tests PHPUnit/Pest para localización
- [ ] Tests de rutas localizadas
- [ ] Tests de embeddings multiidioma
- [ ] Cache de traducciones configurado
- [ ] Performance testing (queries N+1, embeddings)
- [ ] Comando: `translations:cache`
- [ ] Service: `AutoTranslateService` (opcional)
- [ ] Documentación completa: `LOCALIZATION.md`
- [ ] CI/CD actualizado (si aplica)

**📝 Archivos a crear:**
```
tests/Feature/LocalizationTest.php
tests/Feature/PropertyTranslationTest.php
tests/Feature/MultilingualEmbeddingsTest.php
app/Console/Commands/CacheTranslations.php
app/Services/AutoTranslateService.php (opcional)
LOCALIZATION.md
```

**🧪 Tests a implementar:**
```php
// tests/Feature/LocalizationTest.php
test('routes work with locale prefix', function () {
    $response = $this->get('/es/search-properties');
    $response->assertStatus(200);
    
    $response = $this->get('/en/search-properties');
    $response->assertStatus(200);
});

test('locale switches correctly', function () {
    $this->get('/es/propiedad/1');
    expect(app()->getLocale())->toBe('es');
    
    $this->get('/en/property/1');
    expect(app()->getLocale())->toBe('en');
});

test('property shows translated content', function () {
    $property = PropertyListing::factory()->create([
        'title_i18n' => json_encode([
            'es' => 'Casa Moderna',
            'en' => 'Modern House'
        ])
    ]);
    
    $response = $this->get('/es/propiedad/' . $property->id);
    $response->assertSee('Casa Moderna');
    
    $response = $this->get('/en/property/' . $property->id);
    $response->assertSee('Modern House');
});
```

**🚀 Performance Optimizations:**
```bash
# Cache de configuración
php artisan config:cache

# Cache de rutas
php artisan route:cache

# Cache de vistas
php artisan view:cache

# Cache de traducciones
php artisan translations:cache

# Optimizar autoloader
composer dump-autoload -o
```

**📊 Benchmarking:**
```bash
# Instalar Laravel Debugbar (si no está)
composer require barryvdh/laravel-debugbar --dev

# Medir queries y tiempo de carga
# Objetivo: < 200ms por página
# Objetivo: < 50 queries por página (idealmente < 20)
```

**📚 Documentación final:**
- Actualizar `README.md` con instrucciones multiidioma
- Crear `LOCALIZATION.md` con arquitectura y uso
- Actualizar `CLAUDE.md` con nuevas customizaciones
- Documentar API de traducciones

**⏱️ Tiempo estimado:** 6-8 horas

---

## 🚀 Comandos de Inicio Rápido

### Empezar un nuevo día
```bash
# 1. Actualizar este archivo
# Cambiar estado del día a 🔄 En Progreso

# 2. Crear branch (opcional)
git checkout -b i18n/day-X-descripcion

# 3. Verificar entorno
php artisan --version
php artisan migrate:status
npm run dev

# 4. Abrir archivos del día
# Ver sección "Archivos a crear/modificar" del día

# 5. Commit inicial del día
git commit --allow-empty -m "[Day X] Starting: título del día"
```

### Durante el día
```bash
# Commits frecuentes (cada feature pequeña)
git add .
git commit -m "[Day X] Implemented: descripción breve"

# Testing continuo
php artisan test
php artisan serve # testing manual

# Limpiar caches si algo no funciona
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

### Finalizar el día
```bash
# 1. Testing general
php artisan test

# 2. Actualizar este archivo
# Marcar tareas completadas [x]
# Cambiar estado a ✅ Completado
# Agregar notas si es necesario

# 3. Commit final del día
git add .
git commit -m "[Day X] Completed: título del día"
git push origin i18n/day-X-descripcion

# 4. Merge a main (opcional, o esperar fin de semana)
git checkout main
git merge i18n/day-X-descripcion
git push origin main
```

### Si te atrasas
```bash
# No pasa nada, ajusta el siguiente día
# Marca el día actual como ⚠️ En Progreso
# Agrega una nota con lo que falta
# Continúa al día siguiente con las tareas pendientes
```

---

## 🏗️ Arquitectura de Decisiones

### ¿Por qué JSON en BD y no tabla separada?

**Decisión:** Usar columnas JSON (`title_i18n`, `description_i18n`)

**Razones:**
- ✅ Más simple para 2 idiomas
- ✅ Mejor performance (menos JOINs)
- ✅ Más fácil de cachear
- ✅ Laravel tiene excelente soporte JSON

**Cuándo usar tabla separada:**
- Si planeas más de 3-4 idiomas
- Si necesitas traducciones colaborativas
- Si quieres historial de traducciones

### ¿Por qué embeddings separados y no uno solo?

**Decisión:** Columnas `embedding_es` y `embedding_en` separadas

**Razones:**
- ✅ Mejor precisión de búsqueda (50-60% más relevante)
- ✅ Permite búsqueda por idioma específico
- ✅ Evita "mezcla" de idiomas en resultados
- ❌ Más costoso en OpenAI API (2x embeddings)
- ❌ Más espacio en BD (2x vectores)

**Trade-off aceptable** porque la calidad de matching es crítica.

### ¿Por qué prefijo en URL y no subdominios?

**Decisión:** `/es/propiedad` en vez de `es.tudominio.com`

**Razones:**
- ✅ Más simple de configurar
- ✅ No requiere certificados SSL adicionales
- ✅ Mejor para compartir sesión/auth
- ✅ Más común en Laravel
- ✅ Mejor para SEO (domain authority compartido)

**Subdominios** serían mejor si:
- Cada idioma es un "sitio" completamente diferente
- Necesitas servidores por región
- Quieres analytics separados por idioma

### ¿Por qué Alpine.js para tabs y no Livewire?

**Decisión:** Tabs de idioma con Alpine.js

**Razones:**
- ✅ Más rápido (sin round-trip al servidor)
- ✅ Mejor UX (cambio instantáneo)
- ✅ Menos carga en servidor
- ✅ Ya está incluido en el proyecto

**Livewire** sería mejor si:
- Necesitas validación en tiempo real por idioma
- Quieres preview side-by-side
- Necesitas auto-save

---

## 🐛 Troubleshooting

### Problema: "Locale no cambia al cambiar URL"
```bash
# Verificar middleware
php artisan route:list | grep locale

# Verificar que SetLocale esté registrado
# app/Http/Kernel.php → $middlewareGroups['web']

# Debug en navegador
# Agregar en SetLocale::handle():
\Log::info('Locale changed to: ' . app()->getLocale());
```

### Problema: "Traducciones no se muestran, aparece la clave"
```bash
# Limpiar cache de vistas
php artisan view:clear

# Verificar que archivo exista
ls -la lang/es/properties.php

# Verificar sintaxis del archivo
php -l lang/es/properties.php

# Verificar en tinker
php artisan tinker
>>> __('properties.types.house')
```

### Problema: "JSON no se guarda correctamente en BD"
```bash
# Verificar cast en modelo
# protected $casts = ['title_i18n' => 'array'];

# Verificar columna en BD
php artisan tinker
>>> Schema::getColumnType('property_listings', 'title_i18n')
# Debe ser: 'json'

# Testing manual
>>> $property = PropertyListing::first()
>>> $property->title_i18n = ['es' => 'Test', 'en' => 'Test']
>>> $property->save()
>>> $property->fresh()->title_i18n
```

### Problema: "Embeddings no se generan al guardar"
```bash
# Verificar que Observer esté registrado
# app/Providers/AppServiceProvider.php

# Verificar API key de OpenAI
php artisan tinker
>>> config('openai.api_key')

# Forzar regeneración manual
php artisan properties:regenerate-embeddings --limit=1

# Ver logs
tail -f storage/logs/laravel.log
```

### Problema: "Performance lento al cambiar idioma"
```bash
# Implementar cache
# En PropertyController::show()
$property = Cache::remember(
    "property.{$id}.{$locale}",
    3600,
    fn() => PropertyListing::find($id)
);

# Cache de traducciones
php artisan translations:cache

# Verificar queries N+1
# Usar Laravel Debugbar
composer require barryvdh/laravel-debugbar --dev
```

### Problema: "SEO tags no aparecen en HTML"
```bash
# Verificar que layout tenga @yield('seo') o {!! $seo !!}

# Verificar en navegador
curl -s http://localhost/es/propiedad/1 | grep 'hreflang'
curl -s http://localhost/es/propiedad/1 | grep 'og:locale'

# Debug en controller
dd($seo); // antes de return view()
```

---

## 📞 Contacto y Soporte

**Para continuar el trabajo otro día:**
1. Lee la sección del día correspondiente
2. Revisa los archivos marcados como "a crear/modificar"
3. Consulta la sección de Testing
4. Si tienes dudas, revisa Troubleshooting

**Progreso tracking:**
- Actualiza la tabla de "Progreso General" al inicio
- Marca checkboxes conforme completes tareas
- Agrega notas en la columna "Notas" si hay bloqueos

**Recursos útiles:**
- Laravel Docs: https://laravel.com/docs/localization
- OpenAI Embeddings: https://platform.openai.com/docs/guides/embeddings
- Filament Docs: https://filamentphp.com/docs
- Alpine.js: https://alpinejs.dev

---

## 📝 Notas Generales

### Convenciones de código

**Nombres de archivos de traducción:**
- `properties.php` → Todo relacionado con propiedades
- `dashboard.php` → Todo del panel de usuario
- `messages.php` → Mensajes flash, notificaciones
- `seo.php` → Meta tags, descriptions

**Estructura de arrays de traducción:**
```php
return [
    'section' => [
        'subsection' => [
            'key' => 'Valor traducido'
        ]
    ]
];

// Uso: __('properties.types.house')
```

**Nombres de columnas JSON:**
- Sufijo `_i18n` para campos traducibles
- Ejemplo: `title_i18n`, `description_i18n`
- Evitar sufijos `_es`, `_en` (usamos JSON en su lugar)

**Nombres de embeddings:**
- Sufijo con código de idioma
- Ejemplo: `embedding_es`, `embedding_en`
- Tipo: `vector(1536)` para OpenAI ada-002

### Buenas prácticas

**En Blade:**
```blade
✅ Correcto:
{{ __('properties.types.house') }}
{{ __('messages.welcome', ['name' => $user->name]) }}

❌ Evitar:
Casa
"Casa"
Hardcoded strings
```

**En Controladores:**
```php
✅ Correcto:
$locale = app()->getLocale();
$title = $property->getTranslation('title', $locale);

❌ Evitar:
$title = $property->title_es;
Asumir idioma sin verificar
```

**En Rutas:**
```php
✅ Correcto:
route('property.show', ['locale' => app()->getLocale(), 'id' => 1])

❌ Evitar:
route('property.show', ['id' => 1]) // Falta locale
```

### Testing checklist por día

Cada día debe pasar estos tests básicos:
```bash
# 1. Syntax check
find app lang -name "*.php" -exec php -l {} \; | grep -v "No syntax errors"

# 2. PHPUnit
php artisan test

# 3. Manual navigation
php artisan serve
# Navegar a /es y /en

# 4. Git check
git status
git diff
```

### Respaldo y seguridad

**Antes de empezar cada día:**
```bash
# Backup de BD
php artisan backup:database # (si tienes comando)
# O manual:
pg_dump tubasededatos > backup_dia_X.sql

# Tag de Git por día
git tag -a day-X -m "Backup before Day X"
git push origin --tags
```

**Si algo sale mal:**
```bash
# Revertir al estado anterior
git checkout day-X
# O revertir commit específico
git revert <commit-hash>
```

---

## 🎯 Meta del Proyecto

**Objetivo final:** 
Plataforma inmobiliaria completamente bilingüe (ES/EN) con:
- ✅ URLs localizadas SEO-friendly
- ✅ Búsqueda semántica cross-language
- ✅ Matching inteligente multiidioma
- ✅ UI/UX fluida en ambos idiomas
- ✅ Contenido indexable por Google en ambos idiomas
- ✅ Emails y notificaciones localizadas

**KPIs de éxito:**
- Todas las rutas funcionan en `/es` y `/en`
- Búsqueda encuentra resultados relevantes en ambos idiomas
- Sin strings hardcodeados en código
- Performance < 200ms por página
- Tests de localización al 100%
- Documentación completa

---

**¡Éxito con la implementación! 🚀**

*Última actualización: 2025-10-16*
