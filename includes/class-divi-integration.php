<?php

/**
 * Clase para integrar el plugin con Divi
 */

if (!defined('ABSPATH')) {
    exit;
}

class ECP_Divi_Integration
{

    public function __construct()
    {
        add_action('et_builder_ready', array($this, 'register_divi_modules'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_divi_scripts'));
        add_filter('et_pb_all_fields_unprocessed_et_pb_ecp_events', array($this, 'add_divi_module_fields'));
    }

    /**
     * Registrar módulos de Divi
     */
    public function register_divi_modules()
    {
        if (class_exists('ET_Builder_Module')) {
            require_once ECP_PLUGIN_PATH . 'includes/divi/class-et-pb-ecp-events.php';
            require_once ECP_PLUGIN_PATH . 'includes/divi/class-et-pb-ecp-event-card.php';
            require_once ECP_PLUGIN_PATH . 'includes/divi/class-et-pb-ecp-upcoming-events.php';
        }
    }

    /**
     * Encolar scripts específicos de Divi
     */
    public function enqueue_divi_scripts()
    {
        if (et_core_is_fb_enabled() || is_admin()) {
            wp_enqueue_script('ecp-divi', ECP_PLUGIN_URL . 'assets/js/divi.js', array('jquery'), ECP_VERSION, true);
        }
    }

    /**
     * Crear entrada de blog automáticamente con Divi
     */
    public static function create_divi_blog_post($event_id, $event_data)
    {
        $post_data = array(
            'post_title' => $event_data['title'],
            'post_content' => self::generate_divi_content($event_data),
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_author' => get_current_user_id(),
            'meta_input' => array(
                'ecp_event_id' => $event_id,
                'ecp_event_date' => $event_data['event_date'],
                'ecp_event_location' => $event_data['location'],
                'ecp_registration_link' => $event_data['registration_link'],
                '_et_pb_use_builder' => 'on',
                '_et_pb_old_content' => '',
                '_et_pb_page_layout' => 'et_full_width_page'
            )
        );

        $post_id = wp_insert_post($post_data);

        if ($post_id) {
            // Actualizar el evento con el ID del post
            ECP_Database::update_event($event_id, array('blog_post_id' => $post_id));

            // Establecer imagen destacada si hay flyer
            if (isset($event_data['flyer_url']) && $event_data['flyer_url']) {
                self::set_featured_image_from_url($post_id, $event_data['flyer_url']);
            }

            return $post_id;
        }

        return false;
    }

    /**
     * Generar contenido de Divi para el evento
     */
    private static function generate_divi_content($event_data)
    {
        $event_date = date('d/m/Y H:i', strtotime($event_data['event_date']));
        $event_date_formatted = date('l, F j, Y', strtotime($event_data['event_date']));
        $event_time = date('g:i A', strtotime($event_data['event_date']));

        $content = '[et_pb_section admin_label="Evento Header" fullwidth="on" specialty="off"][et_pb_fullwidth_header admin_label="Evento Header" title="' . esc_attr($event_data['title']) . '" subhead="' . esc_attr($event_date_formatted . ' a las ' . $event_time) . '" background_layout="dark" text_orientation="center" header_fullscreen="off" header_scroll_down="off" scroll_down_icon="%%3%%" background_image="' . (isset($event_data['flyer_url']) ? esc_url($event_data['flyer_url']) : '') . '" background_overlay_color="rgba(0,0,0,0.5)" parallax="on" parallax_method="off" custom_button_one="on" button_text_one="Registrarse" button_link_one="' . esc_url($event_data['registration_link']) . '" button_one_icon_placement="right" button_one_on_hover="on" button_one_letter_spacing_hover="0px" /][/et_pb_section]';

        $content .= '[et_pb_section admin_label="Evento Info" fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Descripción del Evento" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"]';
        $content .= '<h2>Detalles del Evento</h2>';
        $content .= '<p><strong>Fecha:</strong> ' . $event_date . '</p>';

        if (!empty($event_data['location'])) {
            $content .= '<p><strong>Lugar:</strong> ' . esc_html($event_data['location']) . '</p>';
        }

        $content .= '<div class="event-description">' . wp_kses_post($event_data['description']) . '</div>';
        $content .= '[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]';

        // Agregar sección de registro si no hay link externo
        if (empty($event_data['registration_link'])) {
            $content .= '[et_pb_section admin_label="Registro" fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Formulario de Registro" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"]';
            $content .= '<h2>Registrarse para el Evento</h2>';
            $content .= '[ecp_event_registration event_id="' . $event_data['id'] . '"]';
            $content .= '[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]';
        }

        // Agregar links de información si existen
        if (!empty($event_data['info_links'])) {
            $info_links = self::parse_info_links($event_data['info_links']);
            if (!empty($info_links)) {
                $content .= '[et_pb_section admin_label="Enlaces" fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Enlaces de Información" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"]';
                $content .= '<h2>Enlaces de Información</h2>';
                $content .= '<ul>';
                foreach ($info_links as $link) {
                    $content .= '<li><a href="' . esc_url($link['url']) . '" target="_blank">' . esc_html($link['title']) . '</a></li>';
                }
                $content .= '</ul>';
                $content .= '[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]';
            }
        }

        return $content;
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
     * Establecer imagen destacada desde URL
     */
    private static function set_featured_image_from_url($post_id, $image_url)
    {
        $image_id = self::upload_image_from_url($image_url);
        if ($image_id) {
            set_post_thumbnail($post_id, $image_id);
        }
    }

    /**
     * Subir imagen desde URL
     */
    private static function upload_image_from_url($image_url)
    {
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);
        $filename = basename($image_url);

        if (wp_mkdir_p($upload_dir['path'])) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }

        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $file);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }

    /**
     * Crear página de eventos con Divi
     */
    public static function create_events_page_with_divi()
    {
        $page_data = array(
            'post_title' => 'Eventos',
            'post_content' => self::generate_events_page_content(),
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_name' => 'eventos',
            'meta_input' => array(
                '_et_pb_use_builder' => 'on',
                '_et_pb_old_content' => '',
                '_et_pb_page_layout' => 'et_full_width_page'
            )
        );

        $page_id = wp_insert_post($page_data);

        if ($page_id) {
            // Establecer como página de eventos
            update_option('ecp_events_page_id', $page_id);
        }

        return $page_id;
    }

    /**
     * Generar contenido de la página de eventos
     */
    private static function generate_events_page_content()
    {
        $content = '[et_pb_section admin_label="Eventos Header" fullwidth="on" specialty="off"][et_pb_fullwidth_header admin_label="Eventos Header" title="Nuestros Eventos" subhead="Descubre los próximos eventos y conferencias de nuestra empresa" background_layout="dark" text_orientation="center" header_fullscreen="off" header_scroll_down="off" scroll_down_icon="%%3%%" background_overlay_color="rgba(0,0,0,0.5)" parallax="on" parallax_method="off" /][/et_pb_section]';

        $content .= '[et_pb_section admin_label="Eventos Lista" fullwidth="off" specialty="off"][et_pb_row][et_pb_column type="4_4"][et_pb_text admin_label="Lista de Eventos" background_layout="light" text_orientation="left" use_border_color="off" border_color="#ffffff" border_style="solid"]';
        $content .= '[ecp_events_list limit="12" future_only="true" show_registration="true" layout="grid" columns="3"]';
        $content .= '[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section]';

        return $content;
    }

    /**
     * Agregar campos personalizados a los módulos de Divi
     */
    public function add_divi_module_fields($fields)
    {
        $fields['events_limit'] = array(
            'label' => 'Número de Eventos',
            'type' => 'text',
            'option_category' => 'basic_option',
            'description' => 'Número de eventos a mostrar',
            'default' => '6'
        );

        $fields['show_registration'] = array(
            'label' => 'Mostrar Registro',
            'type' => 'yes_no_button',
            'option_category' => 'basic_option',
            'options' => array(
                'off' => 'No',
                'on' => 'Sí'
            ),
            'description' => 'Mostrar botón de registro',
            'default' => 'on'
        );

        $fields['layout_style'] = array(
            'label' => 'Estilo de Layout',
            'type' => 'select',
            'option_category' => 'basic_option',
            'options' => array(
                'grid' => 'Grid',
                'list' => 'Lista',
                'masonry' => 'Masonry'
            ),
            'description' => 'Estilo de presentación de los eventos',
            'default' => 'grid'
        );

        return $fields;
    }
}
