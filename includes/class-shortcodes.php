<?php

/**
 * Clase para manejar los shortcodes del plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class ECP_Shortcodes
{

    public function __construct()
    {
        add_shortcode('ecp_events_list', array($this, 'events_list_shortcode'));
        add_shortcode('ecp_event_card', array($this, 'event_card_shortcode'));
        add_shortcode('ecp_upcoming_events', array($this, 'upcoming_events_shortcode'));
        add_shortcode('ecp_event_registration', array($this, 'event_registration_shortcode'));
    }

    /**
     * Shortcode para mostrar lista de eventos
     */
    public function events_list_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'limit' => 12,
            'future_only' => 'true',
            'show_registration' => 'true',
            'layout' => 'grid', // grid, list, masonry
            'columns' => 3,
            'show_past' => 'false'
        ), $atts);

        $args = array(
            'limit' => intval($atts['limit']),
            'future_only' => $atts['future_only'] === 'true',
            'status' => 'active'
        );

        if ($atts['show_past'] === 'true') {
            $args['future_only'] = false;
        }

        $events = ECP_Public::get_events_for_display($args);

        if (empty($events)) {
            return '<div class="ecp-no-events"><p>No hay eventos disponibles en este momento.</p></div>';
        }

        ob_start();
?>
        <div class="ecp-events-container" data-layout="<?php echo esc_attr($atts['layout']); ?>" data-columns="<?php echo esc_attr($atts['columns']); ?>">
            <div class="ecp-events-grid ecp-grid-<?php echo esc_attr($atts['columns']); ?>">
                <?php foreach ($events as $event): ?>
                    <?php echo ECP_Public::render_event_card($event, $atts['show_registration'] === 'true'); ?>
                    <?php echo ECP_Public::render_event_modal($event); ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="ecp-registration-modal" id="ecp-registration-modal" style="display: none;">
            <div class="ecp-modal-content">
                <span class="ecp-modal-close">&times;</span>
                <div class="ecp-modal-body">
                    <h3>Registro para el Evento</h3>
                    <div id="ecp-registration-form-container"></div>
                </div>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }

    /**
     * Shortcode para mostrar una tarjeta de evento específico
     */
    public function event_card_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'event_id' => 0,
            'show_registration' => 'true'
        ), $atts);

        if (!$atts['event_id']) {
            return '<p>ID de evento requerido</p>';
        }

        $event = ECP_Database::get_event($atts['event_id']);

        if (!$event) {
            return '<p>Evento no encontrado</p>';
        }

        $events = ECP_Public::get_events_for_display(array('limit' => 1));
        $event->stats = ECP_Database::get_event_stats($event->id);
        $event->formatted_date = date('d/m/Y', strtotime($event->event_date));
        $event->formatted_time = date('H:i', strtotime($event->event_date));
        $event->is_past = strtotime($event->event_date) < time();
        $event->info_links_parsed = ECP_Public::parse_info_links($event->info_links);

        ob_start();
        echo ECP_Public::render_event_card($event, $atts['show_registration'] === 'true');
        echo ECP_Public::render_event_modal($event);
        return ob_get_clean();
    }

    /**
     * Shortcode para mostrar próximos eventos
     */
    public function upcoming_events_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'limit' => 3,
            'show_registration' => 'true',
            'layout' => 'horizontal'
        ), $atts);

        $args = array(
            'limit' => intval($atts['limit']),
            'future_only' => true,
            'status' => 'active'
        );

        $events = ECP_Public::get_events_for_display($args);

        if (empty($events)) {
            return '<div class="ecp-no-events"><p>No hay próximos eventos programados.</p></div>';
        }

        ob_start();
    ?>
        <div class="ecp-upcoming-events ecp-layout-<?php echo esc_attr($atts['layout']); ?>">
            <h3>Próximos Eventos</h3>
            <div class="ecp-events-list">
                <?php foreach ($events as $event): ?>
                    <div class="ecp-event-item">
                        <?php if ($event->flyer_url): ?>
                            <div class="ecp-event-thumbnail">
                                <img src="<?php echo esc_url($event->flyer_url); ?>" alt="<?php echo esc_attr($event->title); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="ecp-event-info">
                            <h4><?php echo esc_html($event->title); ?></h4>
                            <div class="ecp-event-meta">
                                <span class="ecp-event-date"><?php echo $event->formatted_date; ?></span>
                                <?php if ($event->location): ?>
                                    <span class="ecp-event-location"><?php echo esc_html($event->location); ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($event->description): ?>
                                <p class="ecp-event-excerpt"><?php echo wp_kses_post(wp_trim_words($event->description, 15)); ?></p>
                            <?php endif; ?>

                            <div class="ecp-event-actions">
                                <button class="ecp-btn ecp-btn-small ecp-show-details" data-event-id="<?php echo $event->id; ?>">
                                    Ver Detalles
                                </button>

                                <?php if ($event->registration_link): ?>
                                    <a href="<?php echo esc_url($event->registration_link); ?>" class="ecp-btn ecp-btn-small ecp-btn-primary" target="_blank">
                                        Registrarse
                                    </a>
                                <?php elseif ($atts['show_registration'] === 'true'): ?>
                                    <button class="ecp-btn ecp-btn-small ecp-btn-primary ecp-show-registration" data-event-id="<?php echo $event->id; ?>">
                                        Registrarse
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
<?php
        return ob_get_clean();
    }

    /**
     * Shortcode para mostrar formulario de registro
     */
    public function event_registration_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'event_id' => 0
        ), $atts);

        if (!$atts['event_id']) {
            return '<p>ID de evento requerido</p>';
        }

        return ECP_Public::render_registration_form($atts['event_id']);
    }

    /**
     * Método auxiliar para parsear links de información (duplicado desde ECP_Public)
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
}
