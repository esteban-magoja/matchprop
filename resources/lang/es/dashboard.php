<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Translations - Spanish
    |--------------------------------------------------------------------------
    |
    | Traducciones para el panel de control del usuario
    |
    */

    // Menú principal
    'dashboard' => 'Panel de Control',
    'my_listings' => 'Mis Anuncios',
    'my_requests' => 'Mis Solicitudes',
    'matches' => 'Coincidencias',
    'messages' => 'Mensajes',
    'settings' => 'Configuración',
    'logout' => 'Cerrar Sesión',
    
    // Dashboard home
    'welcome' => 'Bienvenido, :name',
    'quick_stats' => 'Estadísticas Rápidas',
    'total_listings' => 'Total de Anuncios',
    'active_requests' => 'Solicitudes Activas',
    'total_matches' => 'Coincidencias Encontradas',
    'recent_activity' => 'Actividad Reciente',
    'no_activity' => 'No hay actividad reciente',
    
    // Anuncios (Listings)
    'listings' => [
        'title' => 'Mis Anuncios',
        'create' => 'Crear Anuncio',
        'edit' => 'Editar Anuncio',
        'delete' => 'Eliminar Anuncio',
        'view' => 'Ver Anuncio',
        'no_listings' => 'No tienes anuncios publicados',
        'create_first' => 'Crea tu primer anuncio',
        'active' => 'Activo',
        'inactive' => 'Inactivo',
        'publish' => 'Publicar',
        'unpublish' => 'Despublicar',
        'featured' => 'Destacado',
        'views' => 'Vistas',
        'created_at' => 'Creado el',
        'updated_at' => 'Actualizado el',
    ],
    
    // Solicitudes (Requests)
    'requests' => [
        'title' => 'Mis Clientes',
        'description' => 'Gestiona tus clientes y solicitudes de búsqueda de propiedades',
        'create' => 'Nueva Solicitud',
        'edit' => 'Editar',
        'delete' => 'Eliminar Solicitud',
        'view' => 'Ver Solicitud',
        'view_matches' => 'Ver Matches',
        'no_requests' => 'No tienes solicitudes',
        'create_first' => 'Crea tu primera solicitud para encontrar la propiedad ideal',
        'create_button' => 'Crear Solicitud',
        'active' => 'Activa',
        'inactive' => 'Inactiva',
        'expired' => 'Expirada',
        'activate' => 'Activar',
        'deactivate' => 'Desactivar',
        'expires_at' => 'Expira',
        'never_expires' => 'Sin expiración',
        'created' => 'Creada',
        'type' => 'Tipo',
        'operation' => 'Operación',
        'budget' => 'Presupuesto',
        'location' => 'Ubicación',
        'client' => 'Cliente',
        'min_bedrooms_short' => 'Min. :count hab.',
        'min_bathrooms_short' => 'Min. :count baños',
        'min_area_short' => 'Min. :aream²',
    ],
    
    // Formulario de solicitud
    'request_form' => [
        'new_title' => 'Nueva Solicitud/Cliente',
        'edit_title' => 'Editar Solicitud',
        'client_data' => 'Datos del Cliente',
        'client_name' => 'Nombre del Cliente',
        'client_email' => 'Email del Cliente',
        'client_phone' => 'Teléfono del Cliente',
        'client_name_placeholder' => 'Juan Pérez',
        'client_email_placeholder' => 'cliente@email.com',
        'client_phone_placeholder' => '+54 9 351 1234567',
        'title_label' => 'Título de la Solicitud',
        'title_placeholder' => 'Ej: Busco casa con jardín en Córdoba',
        'description_label' => 'Descripción',
        'description_placeholder' => 'Describe con detalle qué tipo de propiedad estás buscando...',
        'property_type' => 'Tipo de Propiedad',
        'transaction_type' => 'Tipo de Operación',
        'budget_section' => 'Presupuesto',
        'budget' => 'Presupuesto',
        'min_budget' => 'Presupuesto Mínimo',
        'max_budget' => 'Presupuesto Máximo',
        'currency' => 'Moneda',
        'location_section' => 'Ubicación',
        'location' => 'Ubicación',
        'country' => 'País',
        'province' => 'Provincia',
        'state' => 'Provincia/Estado',
        'city' => 'Ciudad',
        'minimum_features' => 'Características Mínimas (Opcional)',
        'min_bedrooms' => 'Habitaciones Mínimas',
        'min_bathrooms' => 'Baños Mínimos',
        'min_garages' => 'Cocheras Mínimas',
        'min_parking' => 'Cocheras Mínimas',
        'min_area' => 'Área Mínima (m²)',
        'expiration' => 'Fecha de Expiración (Opcional)',
        'save' => 'Guardar Solicitud',
        'update' => 'Actualizar Solicitud',
        'cancel' => 'Cancelar',
        'back' => 'Volver',
        'select_option' => 'Seleccionar...',
        'required' => 'requerido',
        'optional' => 'opcional',
    ],
    
    // Coincidencias (Matches)
    'matches_section' => [
        'title' => 'Coincidencias',
        'for_listing' => 'Coincidencias para',
        'for_request' => 'Propiedades que coinciden',
        'no_matches' => 'No se encontraron coincidencias',
        'match_level' => 'Nivel de Coincidencia',
        'exact_match' => 'Coincidencia Exacta',
        'intelligent_match' => 'Coincidencia Inteligente',
        'flexible_match' => 'Coincidencia Flexible',
        'match_score' => ':score% de coincidencia',
        'reasons' => 'Razones',
        'contact_requester' => 'Contactar Solicitante',
        'requester_info' => 'Información del Solicitante',
        'see_all_matches' => 'Ver Todas las Coincidencias',
        'matches_summary' => 'Resumen de Coincidencias',
    ],
    
    // Mensajes
    'messages_section' => [
        'title' => 'Mensajes',
        'inbox' => 'Bandeja de Entrada',
        'sent' => 'Enviados',
        'no_messages' => 'No tienes mensajes',
        'new_message' => 'Nuevo Mensaje',
        'reply' => 'Responder',
        'delete' => 'Eliminar',
        'mark_read' => 'Marcar como Leído',
        'mark_unread' => 'Marcar como No Leído',
    ],
    
    // Acciones comunes
    'actions' => [
        'create' => 'Crear',
        'edit' => 'Editar',
        'delete' => 'Eliminar',
        'view' => 'Ver',
        'save' => 'Guardar',
        'cancel' => 'Cancelar',
        'confirm' => 'Confirmar',
        'back' => 'Volver',
        'search' => 'Buscar',
        'filter' => 'Filtrar',
        'export' => 'Exportar',
        'import' => 'Importar',
    ],
    
    // Confirmaciones
    'confirmations' => [
        'delete_listing' => '¿Estás seguro de que quieres eliminar este anuncio?',
        'delete_request' => '¿Estás seguro de que quieres eliminar esta solicitud?',
        'delete_message' => '¿Estás seguro de que quieres eliminar este mensaje?',
        'cannot_undo' => 'Esta acción no se puede deshacer.',
    ],
    
    // Tabs de idioma
    'languages' => [
        'spanish' => 'Español',
        'english' => 'English',
        'fill_both' => 'Completa la información en ambos idiomas',
        'spanish_required' => 'Español (requerido)',
        'english_optional' => 'English (opcional)',
    ],
    
    // Mensajes y alertas del dashboard principal
    'alerts' => [
        'welcome_message' => 'Panel de control donde puedes gestionar tus anuncios, solicitudes y ver coincidencias.',
        'email_verified' => '¡Email verificado exitosamente!',
        'email_verified_desc' => 'Tu dirección de email ha sido verificada correctamente. Ya puedes utilizar todas las funcionalidades de la plataforma.',
        'terms_pending_title' => 'Términos y Condiciones Pendientes',
        'terms_pending_desc' => 'Para poder publicar anuncios en la plataforma, debes aceptar nuestros términos y condiciones.',
        'view_accept_terms' => 'Ver y Aceptar Términos',
    ],
    
    // Títulos y descripciones
    'home' => [
        'title' => 'Panel de Control',
        'description' => 'Gestiona tus propiedades y solicitudes y clientes potenciales desde aquí.',
        'my_listings' => 'Mis Anuncios',
        'clients' => 'Clientes',
        'messages' => 'Mensajes',
        'matches' => 'Matches',
        'view_listings' => 'Ver anuncios',
        'publish_listing' => 'Publicar anuncio',
        'view_requests' => 'Ver solicitudes',
        'add_request' => 'Agregar solicitud',
        'unread_messages' => ':count sin leer',
        'view_messages' => 'Ver mensajes',
        'view_matches' => 'Ver matches',
        'role_message' => 'Usted tiene el rol de',
    ],
];
