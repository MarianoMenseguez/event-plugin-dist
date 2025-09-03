<?php

/**
 * Archivo de desinstalación del plugin Event Calendar Plugin
 * Se ejecuta cuando el plugin es eliminado desde el admin de WordPress
 */

// Verificar que se está llamando desde WordPress
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Eliminar tablas de la base de datos
global $wpdb;

$events_table = $wpdb->prefix . 'ecp_events';
$attendees_table = $wpdb->prefix . 'ecp_attendees';

$wpdb->query("DROP TABLE IF EXISTS $attendees_table");
$wpdb->query("DROP TABLE IF EXISTS $events_table");

// Eliminar opciones del plugin
delete_option('ecp_db_version');
delete_option('ecp_settings');
delete_option('ecp_events_page_id');

// Eliminar metadatos de posts relacionados con eventos
$wpdb->delete(
    $wpdb->postmeta,
    array('meta_key' => 'ecp_event_id')
);

$wpdb->delete(
    $wpdb->postmeta,
    array('meta_key' => 'ecp_event_date')
);

$wpdb->delete(
    $wpdb->postmeta,
    array('meta_key' => 'ecp_event_location')
);

$wpdb->delete(
    $wpdb->postmeta,
    array('meta_key' => 'ecp_registration_link')
);

// Eliminar archivos subidos del plugin (opcional)
$upload_dir = wp_upload_dir();
$plugin_upload_dir = $upload_dir['basedir'] . '/ecp-flyers/';

if (is_dir($plugin_upload_dir)) {
    // Eliminar archivos del directorio
    $files = glob($plugin_upload_dir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    // Eliminar directorio
    rmdir($plugin_upload_dir);
}

// Limpiar cache si existe
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}
