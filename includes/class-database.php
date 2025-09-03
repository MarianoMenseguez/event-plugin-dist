<?php

/**
 * Clase para manejar la base de datos del plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class ECP_Database
{

    /**
     * Crear las tablas necesarias
     */
    public static function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Tabla de eventos
        $events_table = $wpdb->prefix . 'ecp_events';
        $events_sql = "CREATE TABLE $events_table (
            id int(11) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text,
            event_date datetime NOT NULL,
            location varchar(255),
            registration_link varchar(500),
            info_links text,
            flyer_url varchar(500),
            blog_post_id int(11) DEFAULT NULL,
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
            status varchar(20) DEFAULT 'registered',
            PRIMARY KEY (id),
            KEY event_id (event_id),
            KEY email (email),
            FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($events_sql);
        dbDelta($attendees_sql);

        // Guardar versión de la base de datos
        update_option('ecp_db_version', '1.0');
    }

    /**
     * Obtener todos los eventos
     */
    public static function get_events($args = array())
    {
        global $wpdb;

        $defaults = array(
            'status' => 'active',
            'limit' => -1,
            'offset' => 0,
            'orderby' => 'event_date',
            'order' => 'ASC',
            'future_only' => false
        );

        $args = wp_parse_args($args, $defaults);

        $table_name = $wpdb->prefix . 'ecp_events';
        $where_conditions = array("1=1");
        $where_values = array();

        if ($args['status']) {
            $where_conditions[] = "status = %s";
            $where_values[] = $args['status'];
        }

        if ($args['future_only']) {
            $where_conditions[] = "event_date >= %s";
            $where_values[] = current_time('mysql');
        }

        $where_clause = implode(' AND ', $where_conditions);
        $order_clause = "ORDER BY {$args['orderby']} {$args['order']}";

        $limit_clause = '';
        if ($args['limit'] > 0) {
            $limit_clause = $wpdb->prepare("LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        }

        $sql = "SELECT * FROM $table_name WHERE $where_clause $order_clause $limit_clause";

        if (!empty($where_values)) {
            $sql = $wpdb->prepare($sql, $where_values);
        }

        return $wpdb->get_results($sql);
    }

    /**
     * Obtener un evento por ID
     */
    public static function get_event($event_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ecp_events';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $event_id));
    }

    /**
     * Crear un nuevo evento
     */
    public static function create_event($data)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ecp_events';

        $defaults = array(
            'title' => '',
            'description' => '',
            'event_date' => '',
            'location' => '',
            'registration_link' => '',
            'info_links' => '',
            'flyer_url' => '',
            'status' => 'active'
        );

        $data = wp_parse_args($data, $defaults);

        $result = $wpdb->insert($table_name, $data);

        if ($result) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * Actualizar un evento
     */
    public static function update_event($event_id, $data)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ecp_events';

        $result = $wpdb->update(
            $table_name,
            $data,
            array('id' => $event_id),
            null,
            array('%d')
        );

        return $result !== false;
    }

    /**
     * Eliminar un evento
     */
    public static function delete_event($event_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ecp_events';

        return $wpdb->delete($table_name, array('id' => $event_id), array('%d'));
    }

    /**
     * Obtener attendees de un evento
     */
    public static function get_event_attendees($event_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ecp_attendees';
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE event_id = %d ORDER BY registration_date DESC",
            $event_id
        ));
    }

    /**
     * Registrar un attendee
     */
    public static function register_attendee($data)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ecp_attendees';

        $defaults = array(
            'event_id' => 0,
            'first_name' => '',
            'last_name' => '',
            'position' => '',
            'company' => '',
            'email' => '',
            'phone' => '',
            'status' => 'registered'
        );

        $data = wp_parse_args($data, $defaults);

        // Verificar si ya está registrado
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE event_id = %d AND email = %s",
            $data['event_id'],
            $data['email']
        ));

        if ($existing) {
            return new WP_Error('already_registered', 'Ya estás registrado para este evento');
        }

        $result = $wpdb->insert($table_name, $data);

        if ($result) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * Obtener estadísticas de eventos
     */
    public static function get_event_stats($event_id)
    {
        global $wpdb;

        $attendees_table = $wpdb->prefix . 'ecp_attendees';

        $total_attendees = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $attendees_table WHERE event_id = %d",
            $event_id
        ));

        return array(
            'total_attendees' => intval($total_attendees)
        );
    }

    /**
     * Exportar attendees a CSV
     */
    public static function export_attendees_csv($event_id)
    {
        $attendees = self::get_event_attendees($event_id);
        $event = self::get_event($event_id);

        if (!$attendees || !$event) {
            return false;
        }

        $filename = 'attendees_' . sanitize_title($event->title) . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Headers
        fputcsv($output, array(
            'Nombre',
            'Apellido',
            'Posición',
            'Empresa',
            'Email',
            'Teléfono',
            'Fecha de Registro'
        ));

        // Data
        foreach ($attendees as $attendee) {
            fputcsv($output, array(
                $attendee->first_name,
                $attendee->last_name,
                $attendee->position,
                $attendee->company,
                $attendee->email,
                $attendee->phone,
                $attendee->registration_date
            ));
        }

        fclose($output);
        exit;
    }
}
