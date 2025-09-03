<?php

/**
 * Plugin Name: Event Calendar Plugin
 * Plugin URI: https://tu-sitio.com
 * Description: Plugin para administrar eventos de la compañía con integración completa con Divi
 * Version: 1.0.0
 * Author: Tu Nombre
 * License: GPL v2 or later
 * Text Domain: event-calendar-plugin
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes del plugin
define('ECP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ECP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('ECP_VERSION', '1.0.0');

// Clase principal del plugin
class EventCalendarPlugin
{

    public function __construct()
    {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    public function init()
    {
        // Cargar archivos necesarios
        $this->load_dependencies();

        // Inicializar componentes
        $this->init_hooks();
        $this->init_admin();
        $this->init_public();
    }

    private function load_dependencies()
    {
        $files = array(
            'includes/class-database.php',
            'includes/class-admin.php',
            'includes/class-public.php',
            'includes/class-shortcodes.php',
            'includes/class-divi-integration.php'
        );
        
        // Load demo setup if in admin
        if (is_admin()) {
            $files[] = 'demo-setup.php';
        }
        
        foreach ($files as $file) {
            $file_path = ECP_PLUGIN_PATH . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            } else {
                // Log error if file doesn't exist
                error_log("Event Calendar Plugin: File not found - " . $file_path);
            }
        }
    }

    private function init_hooks()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    }

    private function init_admin()
    {
        if (is_admin()) {
            new ECP_Admin();
        }
    }

    private function init_public()
    {
        new ECP_Public();
        new ECP_Shortcodes();
        new ECP_Divi_Integration();
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('ecp-style', ECP_PLUGIN_URL . 'assets/css/style.css', array(), ECP_VERSION);
        wp_enqueue_script('ecp-script', ECP_PLUGIN_URL . 'assets/js/script.js', array('jquery'), ECP_VERSION, true);

        // Localizar script para AJAX
        wp_localize_script('ecp-script', 'ecp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ecp_nonce')
        ));
    }

    public function admin_enqueue_scripts()
    {
        wp_enqueue_style('ecp-admin-style', ECP_PLUGIN_URL . 'assets/css/admin.css', array(), ECP_VERSION);
        wp_enqueue_script('ecp-admin-script', ECP_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), ECP_VERSION, true);

        // Datepicker para fechas
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css');
    }

    public function activate()
    {
        // Load dependencies first
        $this->load_dependencies();
        
        // Check if database class exists
        if (class_exists('ECP_Database')) {
            // Crear tablas de base de datos
            ECP_Database::create_tables();
        } else {
            // Fallback: create tables directly
            $this->create_tables_directly();
        }

        // Crear páginas necesarias
        $this->create_pages();

        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Fallback method to create tables directly
     */
    private function create_tables_directly()
    {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Tabla de eventos
        $events_table = $wpdb->prefix . 'ecp_events';
        $events_sql = "CREATE TABLE $events_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text,
            event_date date NOT NULL,
            event_time time NOT NULL,
            location varchar(255),
            registration_link varchar(500),
            social_media_links text,
            flyer_url varchar(500),
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_date (event_date),
            KEY status (status)
        ) $charset_collate;";
        
        // Tabla de attendees
        $attendees_table = $wpdb->prefix . 'ecp_attendees';
        $attendees_sql = "CREATE TABLE $attendees_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            event_id int(11) NOT NULL,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            position varchar(255),
            company varchar(255),
            email varchar(255),
            phone varchar(50),
            registration_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_id (event_id),
            KEY email (email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($events_sql);
        dbDelta($attendees_sql);
    }

    public function deactivate()
    {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    private function create_pages()
    {
        // Crear página de eventos si no existe
        $events_page = get_page_by_path('eventos');
        if (!$events_page) {
            $page_data = array(
                'post_title' => 'Eventos',
                'post_content' => '[ecp_events_list]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => 'eventos'
            );
            wp_insert_post($page_data);
        }
    }
}

// Inicializar el plugin
new EventCalendarPlugin();
