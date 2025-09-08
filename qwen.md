# Wave - SaaS Framework

## Descripción del Proyecto

Wave es un framework SaaS (Software as a Service) construido con Laravel que facilita y agiliza la creación de aplicaciones SaaS. Proporciona características esenciales como autenticación, perfiles de usuario, facturación, planes de suscripción, roles y permisos, entre otros.

## Convenciones de Código y Buenas Prácticas

### Estilo de Código

Wave sigue las convenciones estándar de Laravel y PHP FIG:

- **PSR-12**: Estilo de codificación básico
- **Indentación**: 4 espacios (configurado en `.editorconfig`)
- **Línea final**: Se inserta una nueva línea al final de cada archivo
- **Espacios en blanco**: Se eliminan los espacios en blanco al final de las líneas (excepto en archivos Markdown)
- **Codificación**: UTF-8

### Estructura de Componentes

1. **Componentes de Blade**:
   - Se organizan por funcionalidad en directorios específicos
   - Los nombres de archivos usan `kebab-case` (ej. `user-menu.blade.php`)
   - Se utilizan componentes anónimos cuando sea posible
   - Los componentes reutilizables se colocan en `resources/themes/{theme}/components/`

2. **Páginas con Folio**:
   - Cada archivo Blade en `resources/themes/{theme}/pages/` se convierte en una ruta
   - Se define middleware y nombre de ruta directamente en el archivo PHP del encabezado
   - Las páginas se organizan en directorios que reflejan la estructura de la URL

3. **Componentes Livewire/Volt**:
   - Se utilizan para interactividad sin JavaScript
   - Se definen en `wave/resources/views/livewire/` para componentes del core
   - Se pueden definir directamente en archivos Blade con Volt usando clases anónimas
   - Los nombres de componentes usan `kebab-case`

### Estilo de CSS y Tailwind

- **Tailwind CSS**: Se utiliza como framework principal
- **Configuración**: En `tailwind.config.js` se incluyen todos los directorios relevantes
- **Temas**: Se soportan múltiples temas configurables
- **Directivas personalizadas**: Se pueden agregar animaciones y extensiones en la configuración

### JavaScript y Alpine.js

- **Alpine.js**: Se utiliza para interactividad ligera
- **Estructura de datos**: Se define en atributos `x-data`
- **Métodos**: Se colocan dentro del objeto de datos de Alpine
- **Inicialización**: Se usa `x-init` para inicializar componentes

### Componente de Ejemplo: Toggle Modo Claro/Oscuro

```blade
<!-- resources/themes/anchor/components/app/light-dark-toggle.blade.php -->
<div 
    x-data="{
        theme: 'light',
        toggle() {
            if(this.theme == 'dark'){ 
                this.theme = 'light';
                localStorage.setItem('theme', 'light');
            }else{ 
                this.theme = 'dark';
                localStorage.setItem('theme', 'dark');
            }
        }
    }"
    x-init="
        if(localStorage.getItem('theme')){
            theme = localStorage.getItem('theme');
        }
        if(theme=='system'){
            theme =  'light';
        }
        if(document.documentElement.classList.contains('dark')){ theme='dark'; }
        $watch('theme', function(value){
            if(value == 'dark'){
                document.documentElement.classList.add('dark');
        } else {
                document.documentElement.classList.remove('dark');
            }
        })
    "
    x-on:click="toggle()"
    class="flex items-center px-1 py-2 text-xs rounded-md cursor-pointer select-none hover:bg-zinc-100 dark:hover:bg-zinc-800"
>
    <!-- Contenido del componente -->
</div>
```

## Tecnologías Principales

- **Laravel 12.x**: Framework PHP principal
- **PHP 8.2+**: Versión requerida de PHP
- **PostgreSQL**: Sistema de gestión de base de datos principal
- **pgvector**: Extensión de PostgreSQL para almacenamiento y búsqueda de vectores
- **Livewire 3.x**: Para interactividad en el frontend sin JavaScript
- **Volt**: Componentes Livewire en archivos Blade
- **Folio**: Enrutamiento basado en archivos Blade
- **Filament 4.x**: Panel de administración y tablas
- **Tailwind CSS 4.x**: Framework de CSS utility-first
- **Alpine.js**: Para interactividad ligera en el frontend
- **Vite**: Herramienta de compilación para assets

## Requisitos de las Extensiones de PostgreSQL

Para que la extensión `pgvector` funcione correctamente, se requiere:

1. **Extensiones de PostgreSQL**:
   - `vector`: Extensión `pgvector` para operaciones con vectores

2. **Configuración del Servidor PostgreSQL**:
   - PostgreSQL debe estar configurado para permitir la creación de extensiones
   - La extensión `pgvector` debe estar instalada en el servidor PostgreSQL
   - El usuario de la base de datos debe tener privilegios suficientes para crear extensiones

3. **Migraciones**:
   - Se ha creado una migración específica para habilitar esta extensión automáticamente:
     - `enable_pgvector_extension`

## Estructura de Directorios

```
/var/www/html/wave/
├── app/                      # Aplicación Laravel principal
├── wave/src/                 # Core del framework Wave
├── resources/
│   ├── themes/              # Temas del frontend
│   │   └── anchor/          # Tema principal
│   │       ├── components/  # Componentes reutilizables
│   │       ├── pages/       # Páginas que generan rutas con Folio
│   │       └── partials/    # Vistas parciales
│   └── views/               # Vistas de componentes de Wave
├── routes/                  # Definición de rutas (web.php usa Wave::routes())
├── config/                  # Configuración de la aplicación
└── database/
    └── migrations/          # Migraciones de la base de datos
```

## Enrutamiento con Folio

Wave utiliza Laravel Folio para el enrutamiento, donde cada archivo Blade en `resources/themes/anchor/pages/` se convierte automáticamente en una ruta.

Ejemplo:
- `resources/themes/anchor/pages/dashboard/index.blade.php` → Ruta `/dashboard`
- `resources/themes/anchor/pages/settings/subscription.blade.php` → Ruta `/settings/subscription`

Los archivos de página pueden definir middleware y nombre de ruta directamente:

```php
<?php
    use function Laravel\Folio\{middleware, name};
    middleware('auth');
    name('dashboard');
?>
```

## Interactividad con Livewire y Volt

Wave utiliza Livewire y Volt extensivamente para crear componentes interactivos sin escribir mucho JavaScript.

### Livewire
Componentes más complejos como el checkout de facturación (`wave/resources/views/livewire/billing/checkout.blade.php`) manejan lógica de suscripción con Alpine.js y Livewire.

### Volt
Permite definir componentes Livewire directamente en archivos Blade sin clases PHP separadas:

```php
<?php
    use Livewire\Volt\Component;
    
    new class extends Component
    {
        public function mount(): void
        {
            // Lógica del componente
        }
    }
?>
```

## Componentes Reutilizables

Wave tiene una amplia colección de componentes Blade reutilizables:

### Componentes del Tema Principal (Anchor)
- `resources/themes/anchor/components/`: Componentes del tema principal
  - `app/`: Componentes de la aplicación
    - `alert.blade.php`: Alertas del sistema
    - `container.blade.php`: Contenedores principales
    - `dashboard-card.blade.php`: Tarjetas del dashboard
    - `heading.blade.php`: Encabezados de secciones
    - `light-dark-toggle.blade.php`: Toggle para modo claro/oscuro
    - `message-for-admin.blade.php`: Mensajes específicos para administradores
    - `message-for-subscriber.blade.php`: Mensajes para suscriptores
    - `settings-layout.blade.php`: Layout para páginas de configuración
    - `sidebar.blade.php`: Sidebar de navegación
    - `user-menu.blade.php`: Menú de usuario
  - `marketing/`: Componentes de marketing
    - `sections/`: Secciones completas
      - `features.blade.php`: Sección de características
      - `hero.blade.php`: Sección hero
      - `pricing.blade.php`: Sección de precios
      - `testimonials.blade.php`: Sección de testimonios
  - `elements/`: Elementos básicos
  - `layouts/`: Layouts principales

### Componentes del Core de Wave
- `wave/resources/views/components/`: Componentes del core de Wave
  - `billing/`: Componentes de facturación
    - `billing_cycle_toggle.blade.php`: Toggle para ciclo de facturación
    - `button.blade.php`: Botón de facturación estilizado
  - Componentes de administración

## Temas (Themes)

Wave utiliza un sistema de temas donde cada tema tiene su propia estructura:

```
resources/themes/{theme_name}/
├── components/     # Componentes del tema
├── pages/          # Páginas que generan rutas
├── partials/       # Vistas parciales
├── assets/         # Assets CSS/JS específicos
├── theme.json      # Configuración del tema
└── theme.jpg       # Imagen del tema
```

El tema principal es "anchor" y se encuentra en `resources/themes/anchor/`.

## Integraciones Clave

- **Filament 4.x**: Panel de administración completo para gestión de datos
  - Configurado en `config/filament.php`
  - Utiliza el sistema de archivos definido en `config/filesystems.php`
  - Incluye tablas y formularios para gestión de datos
  - Integración con Livewire para interactividad

- **Stripe/Paddle**: Integración de pasarelas de pago
  - Configurable mediante variables de entorno
  - Componentes de checkout y actualización de suscripciones

- **Laravel Permissions**: Sistema de roles y permisos (spatie/laravel-permission)
  - Gestión de roles y permisos de usuarios
  - Directivas Blade personalizadas (@admin, @subscriber, etc.)

- **Laravel Impersonate**: Funcionalidad para suplantar usuarios
  - Permite a los administradores navegar como otros usuarios
  - Integrado con el middleware y sistema de autenticación

- **Phosphor Icons**: Conjunto de iconos para la interfaz
  - Componentes Blade para iconos Phosphor
  - Utilizado en menus, botones y elementos de UI

- **JWT Auth**: Autenticación mediante tokens JWT
  - Para APIs y acceso programático
  - Integración con tymon/jwt-auth

## Comandos Útiles para el Desarrollo

### Comandos Artisan

```bash
# Iniciar el servidor de desarrollo
php artisan serve

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Crear un nuevo usuario con rol
php artisan app:create-user

# Crear un nuevo rol
php artisan app:create-role

# Listar rutas de Folio
php artisan folio:list

# Crear una nueva página de Folio
php artisan folio:page

# Crear un nuevo componente Livewire
php artisan make:livewire

# Crear un nuevo componente Volt
php artisan make:volt

# Publicar assets de Livewire
php artisan livewire:publish --assets

# Ver información sobre paquetes Filament instalados
php artisan filament:about

# Configurar assets de Filament
php artisan filament:assets

# Actualizar Filament a la última versión
php artisan filament:upgrade

# Limpiar caché de configuración
php artisan config:clear

# Limpiar caché de rutas
php artisan route:clear

# Limpiar caché de vistas
php artisan view:clear

# Limpiar caché completo
php artisan optimize:clear
```

### Comandos NPM

```bash
# Compilar assets en modo desarrollo
npm run dev

# Compilar assets para producción
npm run build
```

### Comandos de Desarrollo Combinados

```bash
# Iniciar servidor de desarrollo con todas las herramientas (usando script definido en composer.json)
composer run dev
```

## Autenticación y Roles

Wave incluye un sistema completo de autenticación con:

- Registro e inicio de sesión de usuarios
- Perfiles de usuario personalizables
- Sistema de roles y permisos (admin, subscriber, etc.)
- Funcionalidad de suplantación de usuarios (impersonation)
- API tokens para acceso programático

Los roles se manejan a través de `spatie/laravel-permission` y se pueden configurar en el panel de administración.