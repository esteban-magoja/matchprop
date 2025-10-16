# Checklist Diario i18n - Quick Reference

Este es tu checklist rápido diario. Para detalles completos, consulta `I18N_IMPLEMENTATION_PLAN.md`

---

## 📅 DÍA 1: Fundamentos y Configuración
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup (15 min)
- [ ] Abrir `I18N_IMPLEMENTATION_PLAN.md` - Día 1
- [ ] Crear branch: `git checkout -b i18n/day-01-foundations`
- [ ] Verificar entorno: `php artisan --version` y `npm run dev`

### Tareas Core (4-6 horas)
- [ ] Crear `app/Http/Middleware/SetLocale.php`
- [ ] Crear `config/locales.php`
- [ ] Actualizar `routes/web.php` con prefijo `{locale}`
- [ ] Registrar middleware en `app/Http/Kernel.php`
- [ ] Crear `app/helpers.php` con funciones: `current_locale()`, `route_localized()`
- [ ] Crear componente `resources/themes/anchor/components/language-switcher.blade.php`
- [ ] Agregar selector a `layouts/app.blade.php` y `layouts/marketing.blade.php`
- [ ] Crear migración: `add_i18n_to_property_listings`
- [ ] Crear migración: `add_i18n_to_property_requests`
- [ ] Ejecutar: `php artisan migrate`

### Testing (30 min)
```bash
php artisan route:list | grep locale
curl http://localhost/es/search-properties
curl http://localhost/en/search-properties
php artisan tinker
>>> Schema::hasColumn('property_listings', 'title_i18n')
```

### End of Day
- [ ] Marcar tareas completadas en `I18N_IMPLEMENTATION_PLAN.md`
- [ ] Commit: `git commit -am "[Day 1] Foundations complete"`
- [ ] Push: `git push origin i18n/day-01-foundations`
- [ ] Actualizar estado a ✅ en tabla de progreso

---

## 📅 DÍA 2: Base de Datos y Modelos
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup
- [ ] Review day 1 work
- [ ] Branch: `git checkout -b i18n/day-02-models`

### Tareas Core (4-5 horas)
- [ ] Crear `app/Traits/Translatable.php`
- [ ] Actualizar `app/Models/PropertyListing.php` (agregar trait, casts, métodos)
- [ ] Actualizar `app/Models/PropertyRequest.php` (agregar trait, casts, métodos)
- [ ] Actualizar `database/factories/PropertyListingFactory.php`
- [ ] Crear `database/seeders/TranslateExistingPropertiesSeeder.php`
- [ ] Actualizar `database/seeders/DatabaseSeeder.php`

### Testing
```bash
php artisan migrate:fresh --seed
php artisan tinker
>>> $p = PropertyListing::first()
>>> $p->getTranslation('title', 'es')
>>> $p->getTranslation('title', 'en')
```

### End of Day
- [ ] Commit y push
- [ ] Actualizar checklist

---

## 📅 DÍA 3: Archivos de Traducción
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup
- [ ] Branch: `git checkout -b i18n/day-03-translations`

### Tareas Core (3-4 horas)
- [ ] Crear `lang/es/properties.php`
- [ ] Crear `lang/en/properties.php`
- [ ] Crear `lang/es/dashboard.php`
- [ ] Crear `lang/en/dashboard.php`
- [ ] Crear `lang/es/messages.php`
- [ ] Crear `lang/en/messages.php`
- [ ] Crear `lang/es/seo.php`
- [ ] Crear `lang/en/seo.php`
- [ ] Actualizar `lang/es/validation.php`
- [ ] Actualizar `lang/en/validation.php`

### Testing
```bash
php artisan tinker
>>> app()->setLocale('es')
>>> __('properties.types.house')
>>> app()->setLocale('en')
>>> __('properties.types.house')
```

### End of Day
- [ ] Commit y push
- [ ] Actualizar checklist

---

## 📅 DÍA 4: Controladores Search & Detail
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup
- [ ] Branch: `git checkout -b i18n/day-04-controllers-public`

### Tareas Core (5-6 horas)
- [ ] Crear `app/Services/SeoService.php`
- [ ] Actualizar `app/Http/Controllers/PropertySearchController.php`
- [ ] Actualizar `app/Http/Controllers/PropertyController.php`
- [ ] Actualizar rutas en `routes/web.php` (agregar slugs)
- [ ] Implementar hreflang tags
- [ ] Implementar meta descriptions por idioma

### Testing
```bash
curl http://localhost/es/propiedad/1/casa-moderna
curl http://localhost/en/property/1/modern-house
curl -s http://localhost/es/propiedad/1 | grep 'hreflang'
```

### End of Day
- [ ] Commit y push
- [ ] Actualizar checklist

---

## 📅 DÍA 5: Controladores Dashboard
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup
- [ ] Branch: `git checkout -b i18n/day-05-controllers-dashboard`

### Tareas Core (6-7 horas)
- [ ] Crear `app/Http/Requests/StorePropertyListingRequest.php`
- [ ] Crear `app/Http/Requests/UpdatePropertyListingRequest.php`
- [ ] Crear `app/Http/Requests/StorePropertyRequestRequest.php`
- [ ] Actualizar `PropertyRequestController.php` (todos los métodos)
- [ ] Actualizar `PropertyMatchController.php`
- [ ] Actualizar `PropertyMessageController.php`

### Testing
```bash
# Testing manual de formularios
php artisan serve
# Crear anuncio con datos en ambos idiomas
```

### End of Day
- [ ] Commit y push
- [ ] Actualizar checklist

---

## 📅 DÍA 6: Vistas Públicas
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup
- [ ] Branch: `git checkout -b i18n/day-06-views-public`
- [ ] Identificar strings hardcodeados: `grep -r "Casa\|Buscar" resources/themes/`

### Tareas Core (6-8 horas)
- [ ] Actualizar `search-property-listings/index.blade.php`
- [ ] Actualizar `property-listings/show.blade.php`
- [ ] Actualizar `layouts/marketing.blade.php`
- [ ] Actualizar `components/property-card.blade.php`
- [ ] Reemplazar TODOS los strings hardcodeados con `__()`

### Testing
```bash
php artisan view:clear
php artisan serve
# Cambiar idioma y verificar que TODO cambie
```

### End of Day
- [ ] Commit y push
- [ ] Actualizar checklist

---

## 📅 DÍA 7: Vistas Dashboard
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup
- [ ] Branch: `git checkout -b i18n/day-07-views-dashboard`

### Tareas Core (7-9 horas) - DÍA MÁS LARGO
- [ ] Actualizar `dashboard/requests/index.blade.php`
- [ ] Actualizar `dashboard/requests/create.blade.php` (agregar tabs Alpine.js)
- [ ] Actualizar `dashboard/requests/edit.blade.php` (agregar tabs)
- [ ] Actualizar `dashboard/requests/show.blade.php`
- [ ] Actualizar `dashboard/matches/index.blade.php`
- [ ] Actualizar `dashboard/matches/show.blade.php`
- [ ] Actualizar `dashboard/messages/index.blade.php`
- [ ] Actualizar `dashboard/messages/show.blade.php`
- [ ] Actualizar `dashboard/index.blade.php`

### Testing
```bash
# Testing completo del dashboard
# 1. Crear anuncio en ES y EN
# 2. Editar anuncio
# 3. Ver matches
# 4. Enviar mensaje
```

### End of Day
- [ ] Commit y push
- [ ] Actualizar checklist
- [ ] ¡Felicitarse! Día más difícil completado 🎉

---

## 📅 DÍA 8: Embeddings Multiidioma
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup
- [ ] Branch: `git checkout -b i18n/day-08-embeddings`

### Tareas Core (5-6 horas)
- [ ] Crear migración: `add_multilingual_embeddings`
- [ ] Ejecutar: `php artisan migrate`
- [ ] Crear `app/Services/EmbeddingService.php`
- [ ] Crear `app/Observers/PropertyListingObserver.php`
- [ ] Registrar observer en `AppServiceProvider.php`
- [ ] Actualizar `app/Services/PropertyMatchingService.php`
- [ ] Crear comando: `app/Console/Commands/RegeneratePropertyEmbeddings.php`

### Testing
```bash
php artisan properties:regenerate-embeddings
php artisan tinker
>>> $p = PropertyListing::first()
>>> $p->embedding_es
>>> $p->embedding_en
```

### End of Day
- [ ] Commit y push
- [ ] Actualizar checklist

---

## 📅 DÍA 9: SEO y Sitemap
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup
- [ ] Branch: `git checkout -b i18n/day-09-seo`

### Tareas Core (4-5 horas)
- [ ] Crear `app/Http/Controllers/SitemapController.php`
- [ ] Crear `app/Services/SchemaService.php`
- [ ] Crear `app/Console/Commands/GenerateSitemaps.php`
- [ ] Crear vistas: `resources/views/sitemap/index.blade.php`
- [ ] Crear vistas: `resources/views/sitemap/properties.blade.php`
- [ ] Agregar rutas de sitemap en `routes/web.php`
- [ ] Actualizar `public/robots.txt`
- [ ] Mejorar `SeoService.php` con Schema.org

### Testing
```bash
php artisan sitemaps:generate
curl http://localhost/sitemap.xml
curl http://localhost/sitemap-es.xml
curl http://localhost/sitemap-en.xml
```

### Validación Externa
- [ ] Google Rich Results Test
- [ ] Schema.org Validator
- [ ] hreflang Testing Tool

### End of Day
- [ ] Commit y push
- [ ] Actualizar checklist

---

## 📅 DÍA 10: Emails y Notificaciones
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup
- [ ] Branch: `git checkout -b i18n/day-10-emails`

### Tareas Core (4-5 horas)
- [ ] Crear migración: `add_locale_to_users`
- [ ] Ejecutar: `php artisan migrate`
- [ ] Actualizar `app/Models/User.php`
- [ ] Agregar selector de idioma en perfil: `settings/profile.blade.php`
- [ ] Crear `resources/views/emails/es/property-match-found.blade.php`
- [ ] Crear `resources/views/emails/en/property-match-found.blade.php`
- [ ] Crear `resources/views/emails/es/message-received.blade.php`
- [ ] Crear `resources/views/emails/en/message-received.blade.php`
- [ ] Actualizar `app/Notifications/PropertyMatchFoundNotification.php`

### Testing
```bash
php artisan tinker
>>> $user = User::first()
>>> $user->locale = 'es'
>>> $user->notify(new PropertyMatchFoundNotification($match))
# Verificar en Mailhog/Mailtrap
```

### End of Day
- [ ] Commit y push
- [ ] Actualizar checklist

---

## 📅 DÍA 11: Filament Admin
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup
- [ ] Branch: `git checkout -b i18n/day-11-filament`

### Tareas Core (5-6 horas)
- [ ] Crear `lang/es/filament.php`
- [ ] Crear `lang/en/filament.php`
- [ ] Actualizar `app/Filament/Resources/PropertyListingResource.php` (tabs)
- [ ] Actualizar tabla de PropertyListingResource (mostrar ambos idiomas)
- [ ] Actualizar filtros y acciones
- [ ] Actualizar widgets si existen

### Testing
```bash
# Acceder a /admin
# Crear/editar property listing
# Verificar que tabs funcionen
# Verificar que se guarden ambas traducciones
```

### End of Day
- [ ] Commit y push
- [ ] Actualizar checklist

---

## 📅 DÍA 12: Testing y Optimización
**Estado:** ⏸️ Pendiente | **Fecha:** _____

### Morning Setup
- [ ] Branch: `git checkout -b i18n/day-12-testing`

### Tareas Core (6-8 horas)
- [ ] Crear `tests/Feature/LocalizationTest.php`
- [ ] Crear `tests/Feature/PropertyTranslationTest.php`
- [ ] Crear `tests/Feature/MultilingualEmbeddingsTest.php`
- [ ] Crear `app/Console/Commands/CacheTranslations.php`
- [ ] Crear `app/Services/AutoTranslateService.php` (opcional)
- [ ] Crear documentación: `LOCALIZATION.md`
- [ ] Actualizar `README.md`
- [ ] Actualizar `CLAUDE.md`

### Testing Completo
```bash
# Suite completa
php artisan test

# Performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan translations:cache

# Benchmarking
# Objetivo: < 200ms por página
# Objetivo: < 20 queries por página
```

### Documentación
- [ ] Escribir guía de uso en `LOCALIZATION.md`
- [ ] Documentar helpers y servicios
- [ ] Documentar comandos artisan nuevos
- [ ] Actualizar diagramas si existen

### End of Day
- [ ] Commit y push
- [ ] Merge a main
- [ ] Celebrar 🎉

---

## 🚀 Comandos Útiles

### Al inicio de cada día
```bash
git status
git pull origin main
git checkout -b i18n/day-XX-nombre
php artisan migrate:status
npm run dev
```

### Durante el día
```bash
# Testing rápido
php artisan test --filter=Localization

# Limpiar caches
php artisan optimize:clear

# Ver logs
tail -f storage/logs/laravel.log

# Commits frecuentes
git add .
git commit -m "[Day X] Feature: descripción"
```

### Al final del día
```bash
php artisan test
git add .
git commit -m "[Day X] Complete: título del día"
git push origin i18n/day-XX-nombre

# Actualizar este archivo:
# Marcar [x] todas las tareas
# Cambiar estado de ⏸️ a ✅
# Agregar fecha y notas
```

---

## 🆘 Quick Troubleshooting

### Locale no cambia
```bash
php artisan route:list | grep locale
# Verificar middleware en Kernel.php
```

### Traducción no aparece
```bash
php artisan view:clear
php artisan tinker
>>> __('properties.types.house')
```

### JSON no se guarda
```bash
# Verificar cast en modelo:
protected $casts = ['title_i18n' => 'array'];
```

### Embeddings no se generan
```bash
# Verificar observer registrado
# Ver AppServiceProvider.php
tail -f storage/logs/laravel.log
```

### Performance lento
```bash
composer require barryvdh/laravel-debugbar --dev
# Verificar queries N+1
```

---

## 📊 Progreso Visual

```
Día 1: [========================================] 100%
Día 2: [                                        ]   0%
Día 3: [                                        ]   0%
Día 4: [                                        ]   0%
Día 5: [                                        ]   0%
Día 6: [                                        ]   0%
Día 7: [                                        ]   0%
Día 8: [                                        ]   0%
Día 9: [                                        ]   0%
Día 10: [                                       ]   0%
Día 11: [                                       ]   0%
Día 12: [                                       ]   0%

Total: [===                                     ]   8%
```

Actualiza esta barra manualmente cada día. Es motivador ver el progreso!

---

## ✅ Criterios de Éxito

Al finalizar los 12 días, debes poder:
- [ ] Navegar toda la app en `/es` y `/en`
- [ ] Crear anuncios en ambos idiomas
- [ ] Buscar y encontrar resultados relevantes en ambos idiomas
- [ ] Ver SEO tags correctos (hreflang, canonical)
- [ ] Recibir emails en idioma preferido
- [ ] Administrar desde Filament en ambos idiomas
- [ ] Pasar todos los tests
- [ ] Tener documentación completa

---

**Quick Start:**
1. Abre día correspondiente en `I18N_IMPLEMENTATION_PLAN.md`
2. Sigue checklist de arriba
3. Consulta troubleshooting si hay problemas
4. Actualiza progreso al final del día

**¡Adelante! 🚀**
