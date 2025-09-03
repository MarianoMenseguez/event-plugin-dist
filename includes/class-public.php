<?php

/**
 * Clase para manejar el frontend del plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class ECP_Public
{

    public function __construct()
    {
        add_action('wp_ajax_ecp_register_attendee', array($this, 'register_attendee'));
        add_action('wp_ajax_nopriv_ecp_register_attendee', array($this, 'register_attendee'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts'));
    }

    /**
     * Encolar scripts del frontend
     */
    public function enqueue_public_scripts()
    {
        wp_enqueue_script('ecp-public', ECP_PLUGIN_URL . 'assets/js/public.js', array('jquery'), ECP_VERSION, true);
        wp_localize_script('ecp-public', 'ecp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ecp_public_nonce')
        ));
    }

    /**
     * Registrar attendee via AJAX
     */
    public function register_attendee()
    {
        // Verificar nonce
        if (!wp_verify_nonce($_POST['nonce'], 'ecp_public_nonce')) {
            wp_die('Error de seguridad');
        }

        $event_id = intval($_POST['event_id']);
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $position = sanitize_text_field($_POST['position']);
        $company = sanitize_text_field($_POST['company']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);

        // Validar datos requeridos
        if (empty($first_name) || empty($last_name) || empty($email) || empty($event_id)) {
            wp_send_json_error('Todos los campos marcados con * son obligatorios');
        }

        // Validar email
        if (!is_email($email)) {
            wp_send_json_error('El email no es válido');
        }

        // Verificar que el evento existe y está activo
        $event = ECP_Database::get_event($event_id);
        if (!$event || $event->status !== 'active') {
            wp_send_json_error('El evento no está disponible para registro');
        }

        // Verificar que el evento es futuro
        if (strtotime($event->event_date) < time()) {
            wp_send_json_error('No se puede registrar para eventos pasados');
        }

        $data = array(
            'event_id' => $event_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'position' => $position,
            'company' => $company,
            'email' => $email,
            'phone' => $phone
        );

        $result = ECP_Database::register_attendee($data);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } elseif ($result) {
            // Enviar email de confirmación
            $this->send_confirmation_email($event, $data);

            wp_send_json_success('Te has registrado exitosamente para el evento');
        } else {
            wp_send_json_error('Error al procesar el registro');
        }
    }

    /**
     * Enviar email de confirmación
     */
    private function send_confirmation_email($event, $attendee_data)
    {
        $to = $attendee_data['email'];
        $subject = 'Confirmación de Registro - ' . $event->title;

        $message = "Hola {$attendee_data['first_name']},\n\n";
        $message .= "Tu registro para el evento '{$event->title}' ha sido confirmado.\n\n";
        $message .= "Detalles del evento:\n";
        $message .= "Fecha: " . date('d/m/Y H:i', strtotime($event->event_date)) . "\n";
        $message .= "Lugar: " . $event->location . "\n\n";

        if ($event->registration_link) {
            $message .= "Link de registro: " . $event->registration_link . "\n\n";
        }

        $message .= "¡Esperamos verte en el evento!\n\n";
        $message .= "Saludos,\n";
        $message .= get_bloginfo('name');

        $headers = array('Content-Type: text/plain; charset=UTF-8');

        wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Obtener eventos para mostrar en el frontend
     */
    public static function get_events_for_display($args = array())
    {
        $defaults = array(
            'limit' => 12,
            'future_only' => true,
            'status' => 'active'
        );

        $args = wp_parse_args($args, $defaults);

        $events = ECP_Database::get_events($args);

        // Agregar información adicional a cada evento
        foreach ($events as $event) {
            $event->stats = ECP_Database::get_event_stats($event->id);
            $event->formatted_date = date('d/m/Y', strtotime($event->event_date));
            $event->formatted_time = date('H:i', strtotime($event->event_date));
            $event->is_past = strtotime($event->event_date) < time();

            // Parsear links de información
            $event->info_links_parsed = self::parse_info_links($event->info_links);
        }

        return $events;
    }

    /**
     * Parsear links de información
     */
    private static function parse_info_links($info_links)
    {
        if (empty($info_links)) {
            return array();
        }

        $links = array();
        $lines = explode("\n", $info_links);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (strpos($line, '|') !== false) {
                list($title, $url) = explode('|', $line, 2);
                $links[] = array(
                    'title' => trim($title),
                    'url' => trim($url)
                );
            } else {
                $links[] = array(
                    'title' => $line,
                    'url' => $line
                );
            }
        }

        return $links;
    }

    /**
     * Renderizar formulario de registro
     */
    public static function render_registration_form($event_id)
    {
        $event = ECP_Database::get_event($event_id);

        if (!$event || $event->status !== 'active') {
            return '<p>Este evento no está disponible para registro.</p>';
        }

        if (strtotime($event->event_date) < time()) {
            return '<p>No se puede registrar para eventos pasados.</p>';
        }

        ob_start();
?>
        <div class="ecp-registration-form">
            <h3>Registrarse para este evento</h3>
            <form id="ecp-register-form" data-event-id="<?php echo $event_id; ?>">
                <div class="ecp-form-row">
                    <div class="ecp-form-group">
                        <label for="first_name">Nombre *</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="ecp-form-group">
                        <label for="last_name">Apellido *</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>

                <div class="ecp-form-row">
                    <div class="ecp-form-group">
                        <label for="position">Posición</label>
                        <input type="text" id="position" name="position">
                    </div>
                    <div class="ecp-form-group">
                        <label for="company">Empresa</label>
                        <input type="text" id="company" name="company">
                    </div>
                </div>

                <div class="ecp-form-row">
                    <div class="ecp-form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="ecp-form-group">
                        <label for="phone">Teléfono</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                </div>

                <div class="ecp-form-actions">
                    <button type="submit" class="ecp-register-btn">Registrarse</button>
                </div>

                <div class="ecp-form-message" style="display: none;"></div>
            </form>
        </div>
    <?php
        return ob_get_clean();
    }

    /**
     * Renderizar tarjeta de evento
     */
    public static function render_event_card($event, $show_registration = true)
    {
        ob_start();
    ?>
        <div class="ecp-event-card" data-event-id="<?php echo $event->id; ?>">
            <?php if ($event->flyer_url): ?>
                <div class="ecp-event-image">
                    <img src="<?php echo esc_url($event->flyer_url); ?>" alt="<?php echo esc_attr($event->title); ?>">
                </div>
            <?php endif; ?>

            <div class="ecp-event-content">
                <h3 class="ecp-event-title"><?php echo esc_html($event->title); ?></h3>

                <div class="ecp-event-meta">
                    <div class="ecp-event-date">
                        <span class="ecp-meta-label">Fecha:</span>
                        <span class="ecp-meta-value"><?php echo $event->formatted_date; ?></span>
                    </div>

                    <div class="ecp-event-time">
                        <span class="ecp-meta-label">Hora:</span>
                        <span class="ecp-meta-value"><?php echo $event->formatted_time; ?></span>
                    </div>

                    <?php if ($event->location): ?>
                        <div class="ecp-event-location">
                            <span class="ecp-meta-label">Lugar:</span>
                            <span class="ecp-meta-value"><?php echo esc_html($event->location); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="ecp-event-attendees">
                        <span class="ecp-meta-label">Registrados:</span>
                        <span class="ecp-meta-value"><?php echo $event->stats['total_attendees']; ?></span>
                    </div>
                </div>

                <?php if ($event->description): ?>
                    <div class="ecp-event-description">
                        <?php echo wp_kses_post(wp_trim_words($event->description, 20)); ?>
                    </div>
                <?php endif; ?>

                <div class="ecp-event-actions">
                    <?php if ($event->registration_link): ?>
                        <a href="<?php echo esc_url($event->registration_link); ?>" class="ecp-btn ecp-btn-primary" target="_blank">
                            Registrarse
                        </a>
                    <?php elseif ($show_registration): ?>
                        <button class="ecp-btn ecp-btn-primary ecp-show-registration">
                            Registrarse
                        </button>
                    <?php endif; ?>

                    <button class="ecp-btn ecp-btn-secondary ecp-show-details">
                        Ver Detalles
                    </button>
                </div>

                <?php if (!empty($event->info_links_parsed)): ?>
                    <div class="ecp-event-links">
                        <?php foreach ($event->info_links_parsed as $link): ?>
                            <a href="<?php echo esc_url($link['url']); ?>" target="_blank" class="ecp-info-link">
                                <?php echo esc_html($link['title']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }

    /**
     * Renderizar modal de detalles del evento
     */
    public static function render_event_modal($event)
    {
        ob_start();
    ?>
        <div class="ecp-modal" id="ecp-event-modal-<?php echo $event->id; ?>" style="display: none;">
            <div class="ecp-modal-content">
                <span class="ecp-modal-close">&times;</span>

                <?php if ($event->flyer_url): ?>
                    <div class="ecp-modal-image">
                        <img src="<?php echo esc_url($event->flyer_url); ?>" alt="<?php echo esc_attr($event->title); ?>">
                    </div>
                <?php endif; ?>

                <div class="ecp-modal-body">
                    <h2><?php echo esc_html($event->title); ?></h2>

                    <div class="ecp-modal-meta">
                        <div class="ecp-modal-date">
                            <strong>Fecha:</strong> <?php echo $event->formatted_date; ?> a las <?php echo $event->formatted_time; ?>
                        </div>

                        <?php if ($event->location): ?>
                            <div class="ecp-modal-location">
                                <strong>Lugar:</strong> <?php echo esc_html($event->location); ?>
                            </div>
                        <?php endif; ?>

                        <div class="ecp-modal-attendees">
                            <strong>Registrados:</strong> <?php echo $event->stats['total_attendees']; ?> personas
                        </div>
                    </div>

                    <?php if ($event->description): ?>
                        <div class="ecp-modal-description">
                            <?php echo wp_kses_post($event->description); ?>
                        </div>
                    <?php endif; ?>

                    <div class="ecp-modal-actions">
                        <?php if ($event->registration_link): ?>
                            <a href="<?php echo esc_url($event->registration_link); ?>" class="ecp-btn ecp-btn-primary" target="_blank">
                                Registrarse
                            </a>
                        <?php else: ?>
                            <button class="ecp-btn ecp-btn-primary ecp-show-registration">
                                Registrarse
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($event->info_links_parsed)): ?>
                        <div class="ecp-modal-links">
                            <h4>Enlaces de información:</h4>
                            <?php foreach ($event->info_links_parsed as $link): ?>
                                <a href="<?php echo esc_url($link['url']); ?>" target="_blank" class="ecp-info-link">
                                    <?php echo esc_html($link['title']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
        return ob_get_clean();
    }
}
