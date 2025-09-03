<?php

/**
 * Archivo de configuración del plugin Event Calendar Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

// Configuración del plugin
define('ECP_CONFIG', array(
    // Versión del plugin
    'version' => '1.0.0',

    // Configuración de base de datos
    'db_version' => '1.0',

    // Configuración de archivos
    'max_file_size' => 5 * 1024 * 1024, // 5MB
    'allowed_file_types' => array('jpg', 'jpeg', 'png', 'gif', 'webp'),

    // Configuración de eventos
    'default_events_per_page' => 12,
    'max_events_per_page' => 50,
    'default_event_status' => 'active',

    // Configuración de emails
    'email_from_name' => get_bloginfo('name'),
    'email_from_email' => get_option('admin_email'),

    // Configuración de cache
    'cache_duration' => 3600, // 1 hora

    // Configuración de seguridad
    'nonce_action' => 'ecp_nonce_action',
    'nonce_name' => 'ecp_nonce',

    // Configuración de Divi
    'divi_module_category' => 'Event Calendar Plugin',

    // Configuración de colores por defecto
    'default_colors' => array(
        'primary' => '#00d4aa',
        'secondary' => '#1a1a1a',
        'accent' => '#ff6b35',
        'text' => '#333333',
        'light_gray' => '#f8f9fa',
        'border' => '#e9ecef'
    ),

    // Configuración de animaciones
    'animation_duration' => 300,
    'stagger_delay' => 100,

    // Configuración de responsive
    'breakpoints' => array(
        'mobile' => 480,
        'tablet' => 768,
        'desktop' => 1024,
        'large' => 1200
    ),

    // Configuración de validación
    'validation' => array(
        'required_fields' => array('title', 'event_date'),
        'email_validation' => true,
        'url_validation' => true,
        'date_validation' => true
    ),

    // Configuración de exportación
    'export_formats' => array('csv', 'xlsx'),
    'export_encoding' => 'UTF-8',

    // Configuración de logs
    'enable_logging' => false,
    'log_level' => 'error',

    // Configuración de performance
    'lazy_loading' => true,
    'image_optimization' => true,
    'minify_assets' => false
));

// Configuración de hooks
define('ECP_HOOKS', array(
    'events' => array(
        'created' => 'ecp_event_created',
        'updated' => 'ecp_event_updated',
        'deleted' => 'ecp_event_deleted',
        'status_changed' => 'ecp_event_status_changed'
    ),
    'attendees' => array(
        'registered' => 'ecp_attendee_registered',
        'updated' => 'ecp_attendee_updated',
        'deleted' => 'ecp_attendee_deleted'
    ),
    'emails' => array(
        'registration_confirmation' => 'ecp_registration_confirmation',
        'event_reminder' => 'ecp_event_reminder',
        'event_cancelled' => 'ecp_event_cancelled'
    )
));

// Configuración de filtros
define('ECP_FILTERS', array(
    'events' => array(
        'query_args' => 'ecp_events_query_args',
        'display_data' => 'ecp_event_display_data',
        'card_template' => 'ecp_event_card_template',
        'list_template' => 'ecp_events_list_template'
    ),
    'attendees' => array(
        'registration_data' => 'ecp_attendee_registration_data',
        'validation_rules' => 'ecp_attendee_validation_rules'
    ),
    'emails' => array(
        'subject' => 'ecp_email_subject',
        'message' => 'ecp_email_message',
        'headers' => 'ecp_email_headers'
    ),
    'forms' => array(
        'registration_form' => 'ecp_registration_form',
        'admin_form' => 'ecp_admin_form'
    )
));

// Configuración de shortcodes
define('ECP_SHORTCODES', array(
    'events_list' => 'ecp_events_list',
    'event_card' => 'ecp_event_card',
    'upcoming_events' => 'ecp_upcoming_events',
    'event_registration' => 'ecp_event_registration'
));

// Configuración de módulos de Divi
define('ECP_DIVI_MODULES', array(
    'events' => 'et_pb_ecp_events',
    'event_card' => 'et_pb_ecp_event_card',
    'upcoming_events' => 'et_pb_ecp_upcoming_events'
));

// Configuración de permisos
define('ECP_CAPABILITIES', array(
    'manage_events' => 'manage_ecp_events',
    'edit_events' => 'edit_ecp_events',
    'delete_events' => 'delete_ecp_events',
    'view_attendees' => 'view_ecp_attendees',
    'export_attendees' => 'export_ecp_attendees'
));

// Configuración de mensajes
define('ECP_MESSAGES', array(
    'success' => array(
        'event_created' => 'Evento creado exitosamente',
        'event_updated' => 'Evento actualizado exitosamente',
        'event_deleted' => 'Evento eliminado exitosamente',
        'attendee_registered' => 'Registro exitoso',
        'settings_saved' => 'Configuración guardada'
    ),
    'error' => array(
        'event_not_found' => 'Evento no encontrado',
        'invalid_data' => 'Datos inválidos',
        'registration_failed' => 'Error en el registro',
        'email_already_registered' => 'Este email ya está registrado',
        'event_past' => 'No se puede registrar para eventos pasados'
    ),
    'info' => array(
        'no_events' => 'No hay eventos disponibles',
        'registration_closed' => 'Registro cerrado',
        'event_full' => 'Evento completo'
    )
));

// Configuración de validación de formularios
define('ECP_VALIDATION_RULES', array(
    'title' => array(
        'required' => true,
        'min_length' => 3,
        'max_length' => 255
    ),
    'description' => array(
        'required' => false,
        'max_length' => 5000
    ),
    'event_date' => array(
        'required' => true,
        'future_date' => true
    ),
    'location' => array(
        'required' => false,
        'max_length' => 255
    ),
    'registration_link' => array(
        'required' => false,
        'url' => true
    ),
    'first_name' => array(
        'required' => true,
        'min_length' => 2,
        'max_length' => 100
    ),
    'last_name' => array(
        'required' => true,
        'min_length' => 2,
        'max_length' => 100
    ),
    'email' => array(
        'required' => true,
        'email' => true,
        'max_length' => 255
    ),
    'position' => array(
        'required' => false,
        'max_length' => 255
    ),
    'company' => array(
        'required' => false,
        'max_length' => 255
    ),
    'phone' => array(
        'required' => false,
        'max_length' => 50
    )
));

// Configuración de templates
define('ECP_TEMPLATES', array(
    'event_card' => 'event-card.php',
    'event_modal' => 'event-modal.php',
    'registration_form' => 'registration-form.php',
    'events_list' => 'events-list.php',
    'upcoming_events' => 'upcoming-events.php'
));

// Configuración de assets
define('ECP_ASSETS', array(
    'css' => array(
        'style' => 'assets/css/style.css',
        'admin' => 'assets/css/admin.css'
    ),
    'js' => array(
        'script' => 'assets/js/script.js',
        'public' => 'assets/js/public.js',
        'admin' => 'assets/js/admin.js',
        'divi' => 'assets/js/divi.js'
    )
));

// Configuración de API
define('ECP_API', array(
    'version' => 'v1',
    'namespace' => 'ecp/v1',
    'endpoints' => array(
        'events' => '/events',
        'attendees' => '/attendees',
        'export' => '/export'
    )
));

// Configuración de cache
define('ECP_CACHE', array(
    'events' => array(
        'key' => 'ecp_events',
        'duration' => 3600
    ),
    'attendees' => array(
        'key' => 'ecp_attendees',
        'duration' => 1800
    ),
    'settings' => array(
        'key' => 'ecp_settings',
        'duration' => 86400
    )
));

// Configuración de logs
define('ECP_LOGS', array(
    'enabled' => false,
    'level' => 'error',
    'file' => 'ecp.log',
    'max_size' => 10485760, // 10MB
    'max_files' => 5
));
