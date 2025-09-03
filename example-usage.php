<?php

/**
 * Archivo de ejemplo de uso del plugin Event Calendar Plugin
 * Este archivo muestra cómo usar las funciones del plugin en tu tema o en otros plugins
 */

// Ejemplo 1: Obtener eventos programados
function get_upcoming_events_example()
{
    $events = ECP_Database::get_events(array(
        'future_only' => true,
        'limit' => 5,
        'status' => 'active'
    ));

    foreach ($events as $event) {
        echo '<h3>' . esc_html($event->title) . '</h3>';
        echo '<p>Fecha: ' . date('d/m/Y H:i', strtotime($event->event_date)) . '</p>';
        echo '<p>Lugar: ' . esc_html($event->location) . '</p>';
        echo '<hr>';
    }
}

// Ejemplo 2: Registrar un attendee programáticamente
function register_attendee_example()
{
    $data = array(
        'event_id' => 1,
        'first_name' => 'Juan',
        'last_name' => 'Pérez',
        'position' => 'Desarrollador',
        'company' => 'Mi Empresa',
        'email' => 'juan@empresa.com',
        'phone' => '+54 11 1234-5678'
    );

    $result = ECP_Database::register_attendee($data);

    if ($result) {
        echo 'Attendee registrado exitosamente con ID: ' . $result;
    } else {
        echo 'Error al registrar attendee';
    }
}

// Ejemplo 3: Crear un evento programáticamente
function create_event_example()
{
    $event_data = array(
        'title' => 'Conferencia de Tecnología 2024',
        'description' => 'Una conferencia sobre las últimas tendencias en tecnología.',
        'event_date' => '2024-06-15 14:00:00',
        'location' => 'Centro de Convenciones',
        'registration_link' => 'https://ejemplo.com/registro',
        'info_links' => "LinkedIn|https://linkedin.com/company/empresa\nTwitter|https://twitter.com/empresa",
        'status' => 'active'
    );

    $event_id = ECP_Database::create_event($event_data);

    if ($event_id) {
        echo 'Evento creado exitosamente con ID: ' . $event_id;

        // Crear entrada de blog automáticamente
        $blog_post_id = ECP_Divi_Integration::create_divi_blog_post($event_id, $event_data);
        if ($blog_post_id) {
            echo 'Entrada de blog creada con ID: ' . $blog_post_id;
        }
    } else {
        echo 'Error al crear evento';
    }
}

// Ejemplo 4: Obtener estadísticas de un evento
function get_event_stats_example()
{
    $event_id = 1;
    $stats = ECP_Database::get_event_stats($event_id);

    echo 'Total de attendees: ' . $stats['total_attendees'];
}

// Ejemplo 5: Filtrar eventos por fecha
function filter_events_by_date_example()
{
    $start_date = '2024-01-01';
    $end_date = '2024-12-31';

    $events = ECP_Database::get_events(array(
        'future_only' => false,
        'status' => 'active'
    ));

    $filtered_events = array();
    foreach ($events as $event) {
        $event_date = date('Y-m-d', strtotime($event->event_date));
        if ($event_date >= $start_date && $event_date <= $end_date) {
            $filtered_events[] = $event;
        }
    }

    return $filtered_events;
}

// Ejemplo 6: Usar shortcodes en PHP
function display_events_shortcode_example()
{
    // Mostrar lista de eventos
    echo do_shortcode('[ecp_events_list limit="6" future_only="true" layout="grid" columns="3"]');

    // Mostrar próximos eventos
    echo do_shortcode('[ecp_upcoming_events limit="3" layout="horizontal"]');

    // Mostrar evento específico
    echo do_shortcode('[ecp_event_card event_id="1" show_registration="true"]');
}

// Ejemplo 7: Personalizar el formulario de registro
function customize_registration_form_example()
{
    add_filter('ecp_registration_form', function ($form_html, $event_id) {
        // Agregar campo personalizado
        $custom_field = '<div class="ecp-form-group">
            <label for="dietary_requirements">Requisitos Dietéticos</label>
            <textarea id="dietary_requirements" name="dietary_requirements" rows="3"></textarea>
        </div>';

        // Insertar antes del botón de envío
        $form_html = str_replace('<div class="ecp-form-actions">', $custom_field . '<div class="ecp-form-actions">', $form_html);

        return $form_html;
    }, 10, 2);
}

// Ejemplo 8: Personalizar email de confirmación
function customize_confirmation_email_example()
{
    add_filter('ecp_registration_email_subject', function ($subject, $event, $attendee) {
        return 'Confirmación de Registro - ' . $event->title . ' - ' . $attendee['first_name'];
    }, 10, 3);

    add_filter('ecp_registration_email_message', function ($message, $event, $attendee) {
        $custom_message = "Hola {$attendee['first_name']},\n\n";
        $custom_message .= "Tu registro para el evento '{$event->title}' ha sido confirmado.\n\n";
        $custom_message .= "Detalles del evento:\n";
        $custom_message .= "Fecha: " . date('d/m/Y H:i', strtotime($event->event_date)) . "\n";
        $custom_message .= "Lugar: " . $event->location . "\n\n";
        $custom_message .= "¡Esperamos verte en el evento!\n\n";
        $custom_message .= "Saludos,\n";
        $custom_message .= get_bloginfo('name');

        return $custom_message;
    }, 10, 3);
}

// Ejemplo 9: Agregar hook personalizado
function add_custom_hook_example()
{
    add_action('ecp_attendee_registered', function ($attendee_id, $event_id, $attendee_data) {
        // Enviar notificación al administrador
        $admin_email = get_option('admin_email');
        $subject = 'Nuevo registro para evento';
        $message = "Se ha registrado un nuevo attendee:\n\n";
        $message .= "Nombre: {$attendee_data['first_name']} {$attendee_data['last_name']}\n";
        $message .= "Email: {$attendee_data['email']}\n";
        $message .= "Empresa: {$attendee_data['company']}\n";

        wp_mail($admin_email, $subject, $message);
    }, 10, 3);
}

// Ejemplo 10: Crear widget personalizado
class ECP_Custom_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'ecp_custom_widget',
            'Eventos Personalizados',
            array('description' => 'Muestra eventos con configuración personalizada')
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $limit = !empty($instance['limit']) ? $instance['limit'] : 3;
        $show_registration = !empty($instance['show_registration']) ? 'true' : 'false';

        echo do_shortcode("[ecp_upcoming_events limit=\"{$limit}\" show_registration=\"{$show_registration}\"]");

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $limit = !empty($instance['limit']) ? $instance['limit'] : 3;
        $show_registration = !empty($instance['show_registration']) ? $instance['show_registration'] : false;
?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Título:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>">Número de eventos:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo esc_attr($limit); ?>" min="1" max="10">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_registration); ?> id="<?php echo $this->get_field_id('show_registration'); ?>" name="<?php echo $this->get_field_name('show_registration'); ?>" />
            <label for="<?php echo $this->get_field_id('show_registration'); ?>">Mostrar botón de registro</label>
        </p>
<?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['limit'] = (!empty($new_instance['limit'])) ? intval($new_instance['limit']) : 3;
        $instance['show_registration'] = !empty($new_instance['show_registration']);

        return $instance;
    }
}

// Registrar el widget
function register_ecp_custom_widget()
{
    register_widget('ECP_Custom_Widget');
}
add_action('widgets_init', 'register_ecp_custom_widget');

// Ejemplo 11: Crear página de eventos personalizada
function create_custom_events_page()
{
    $page_content = '
    <div class="custom-events-page">
        <h1>Nuestros Eventos</h1>
        
        <div class="events-filters">
            <select id="event-filter">
                <option value="all">Todos los eventos</option>
                <option value="upcoming">Próximos eventos</option>
                <option value="past">Eventos pasados</option>
            </select>
        </div>
        
        <div class="events-grid">
            [ecp_events_list limit="12" future_only="true" layout="grid" columns="3"]
        </div>
    </div>
    
    <style>
    .custom-events-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .events-filters {
        margin-bottom: 30px;
        text-align: center;
    }
    
    .events-filters select {
        padding: 10px;
        border: 2px solid #00d4aa;
        border-radius: 5px;
        font-size: 16px;
    }
    
    .events-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        $("#event-filter").change(function() {
            var filter = $(this).val();
            $(".ecp-event-card").each(function() {
                var eventDate = new Date($(this).data("event-date"));
                var now = new Date();
                
                var show = true;
                if (filter === "upcoming") {
                    show = eventDate > now;
                } else if (filter === "past") {
                    show = eventDate < now;
                }
                
                if (show) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
    </script>
    ';

    return $page_content;
}

// Ejemplo 12: Integración con otros plugins
function integrate_with_other_plugins_example()
{
    // Integración con WooCommerce
    if (class_exists('WooCommerce')) {
        add_action('woocommerce_thankyou', function ($order_id) {
            // Crear evento automáticamente cuando se completa una compra
            $order = wc_get_order($order_id);
            $event_data = array(
                'title' => 'Evento de Compra - Orden #' . $order_id,
                'description' => 'Evento creado automáticamente por compra en WooCommerce',
                'event_date' => date('Y-m-d H:i:s', strtotime('+1 week')),
                'location' => 'Online',
                'status' => 'active'
            );

            ECP_Database::create_event($event_data);
        });
    }

    // Integración con Contact Form 7
    if (function_exists('wpcf7_add_form_tag')) {
        wpcf7_add_form_tag('ecp_events_select', function ($tag) {
            $events = ECP_Database::get_events(array(
                'future_only' => true,
                'status' => 'active'
            ));

            $html = '<select name="ecp-event" class="wpcf7-form-control">';
            $html .= '<option value="">Seleccionar evento</option>';

            foreach ($events as $event) {
                $html .= '<option value="' . $event->id . '">' . esc_html($event->title) . '</option>';
            }

            $html .= '</select>';

            return $html;
        });
    }
}

// Ejemplo 13: API REST personalizada
function add_custom_rest_endpoints()
{
    register_rest_route('ecp/v1', '/events/search', array(
        'methods' => 'GET',
        'callback' => function ($request) {
            $search_term = $request->get_param('q');
            $events = ECP_Database::get_events(array(
                'future_only' => true,
                'status' => 'active'
            ));

            $filtered_events = array();
            foreach ($events as $event) {
                if (
                    stripos($event->title, $search_term) !== false ||
                    stripos($event->description, $search_term) !== false
                ) {
                    $filtered_events[] = array(
                        'id' => $event->id,
                        'title' => $event->title,
                        'date' => $event->event_date,
                        'location' => $event->location
                    );
                }
            }

            return rest_ensure_response($filtered_events);
        },
        'permission_callback' => '__return_true'
    ));
}
add_action('rest_api_init', 'add_custom_rest_endpoints');
