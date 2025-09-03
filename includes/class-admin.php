<?php

/**
 * Clase para manejar la administración del plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class ECP_Admin
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_actions'));
        add_action('wp_ajax_ecp_upload_flyer', array($this, 'handle_flyer_upload'));
        add_action('wp_ajax_ecp_export_attendees', array($this, 'export_attendees'));
    }

    /**
     * Agregar menú de administración
     */
    public function add_admin_menu()
    {
        add_menu_page(
            'Eventos',
            'Eventos',
            'manage_options',
            'ecp-events',
            array($this, 'events_page'),
            'dashicons-calendar-alt',
            30
        );

        add_submenu_page(
            'ecp-events',
            'Todos los Eventos',
            'Todos los Eventos',
            'manage_options',
            'ecp-events',
            array($this, 'events_page')
        );

        add_submenu_page(
            'ecp-events',
            'Agregar Evento',
            'Agregar Evento',
            'manage_options',
            'ecp-add-event',
            array($this, 'add_event_page')
        );

        add_submenu_page(
            'ecp-events',
            'Configuración',
            'Configuración',
            'manage_options',
            'ecp-settings',
            array($this, 'settings_page')
        );
    }

    /**
     * Manejar acciones del admin
     */
    public function handle_actions()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['ecp_action'])) {
            $action = sanitize_text_field($_POST['ecp_action']);

            switch ($action) {
                case 'create_event':
                    $this->create_event();
                    break;
                case 'update_event':
                    $this->update_event();
                    break;
                case 'delete_event':
                    $this->delete_event();
                    break;
            }
        }
    }

    /**
     * Página principal de eventos
     */
    public function events_page()
    {
        $events = ECP_Database::get_events(array('limit' => 50));
?>
        <div class="wrap">
            <h1>Gestión de Eventos</h1>
            <a href="<?php echo admin_url('admin.php?page=ecp-add-event'); ?>" class="page-title-action">Agregar Nuevo Evento</a>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Attendees</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="6">No hay eventos registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <?php $stats = ECP_Database::get_event_stats($event->id); ?>
                            <tr>
                                <td><strong><?php echo esc_html($event->title); ?></strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($event->event_date)); ?></td>
                                <td><?php echo esc_html($event->location); ?></td>
                                <td><?php echo $stats['total_attendees']; ?></td>
                                <td>
                                    <span class="status-<?php echo $event->status; ?>">
                                        <?php echo ucfirst($event->status); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=ecp-add-event&edit=' . $event->id); ?>" class="button button-small">Editar</a>
                                    <a href="<?php echo admin_url('admin.php?page=ecp-events&action=export&event_id=' . $event->id); ?>" class="button button-small">Exportar</a>
                                    <a href="<?php echo admin_url('admin.php?page=ecp-events&action=delete&event_id=' . $event->id); ?>" class="button button-small" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <style>
            .status-active {
                color: #46b450;
            }

            .status-inactive {
                color: #dc3232;
            }
        </style>
    <?php
    }

    /**
     * Página para agregar/editar eventos
     */
    public function add_event_page()
    {
        $event_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
        $event = $event_id ? ECP_Database::get_event($event_id) : null;

        if ($event_id && !$event) {
            echo '<div class="notice notice-error"><p>Evento no encontrado</p></div>';
            return;
        }

        $attendees = $event_id ? ECP_Database::get_event_attendees($event_id) : array();
    ?>
        <div class="wrap">
            <h1><?php echo $event ? 'Editar Evento' : 'Agregar Nuevo Evento'; ?></h1>

            <form method="post" action="" enctype="multipart/form-data">
                <?php wp_nonce_field('ecp_event_nonce', 'ecp_nonce'); ?>
                <input type="hidden" name="ecp_action" value="<?php echo $event ? 'update_event' : 'create_event'; ?>">
                <?php if ($event): ?>
                    <input type="hidden" name="event_id" value="<?php echo $event->id; ?>">
                <?php endif; ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="title">Título del Evento *</label></th>
                        <td><input type="text" id="title" name="title" value="<?php echo $event ? esc_attr($event->title) : ''; ?>" class="regular-text" required></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="description">Descripción</label></th>
                        <td>
                            <?php
                            $content = $event ? $event->description : '';
                            wp_editor($content, 'description', array(
                                'textarea_name' => 'description',
                                'media_buttons' => true,
                                'textarea_rows' => 10
                            ));
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="event_date">Fecha y Hora *</label></th>
                        <td>
                            <input type="datetime-local" id="event_date" name="event_date"
                                value="<?php echo $event ? date('Y-m-d\TH:i', strtotime($event->event_date)) : ''; ?>"
                                class="regular-text" required>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="location">Lugar</label></th>
                        <td><input type="text" id="location" name="location" value="<?php echo $event ? esc_attr($event->location) : ''; ?>" class="regular-text"></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="registration_link">Link de Registro</label></th>
                        <td><input type="url" id="registration_link" name="registration_link" value="<?php echo $event ? esc_attr($event->registration_link) : ''; ?>" class="regular-text"></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="info_links">Links de Información (uno por línea)</label></th>
                        <td>
                            <textarea id="info_links" name="info_links" rows="5" class="large-text"><?php echo $event ? esc_textarea($event->info_links) : ''; ?></textarea>
                            <p class="description">Formato: Título|URL (ejemplo: LinkedIn|https://linkedin.com/company/empresa)</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="flyer">Flyer del Evento</label></th>
                        <td>
                            <?php if ($event && $event->flyer_url): ?>
                                <div class="current-flyer">
                                    <img src="<?php echo esc_url($event->flyer_url); ?>" style="max-width: 200px; height: auto;">
                                    <p><a href="<?php echo esc_url($event->flyer_url); ?>" target="_blank">Ver flyer actual</a></p>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="flyer" name="flyer" accept="image/*">
                            <p class="description">Sube una imagen para el flyer del evento</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="status">Estado</label></th>
                        <td>
                            <select id="status" name="status">
                                <option value="active" <?php selected($event ? $event->status : 'active', 'active'); ?>>Activo</option>
                                <option value="inactive" <?php selected($event ? $event->status : 'active', 'inactive'); ?>>Inactivo</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="create_blog_post">Crear Entrada de Blog</label></th>
                        <td>
                            <label>
                                <input type="checkbox" id="create_blog_post" name="create_blog_post" value="1">
                                Crear automáticamente una entrada de blog para este evento
                            </label>
                        </td>
                    </tr>
                </table>

                <?php submit_button($event ? 'Actualizar Evento' : 'Crear Evento'); ?>
            </form>

            <?php if ($event && !empty($attendees)): ?>
                <hr>
                <h2>Attendees Registrados (<?php echo count($attendees); ?>)</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Posición</th>
                            <th>Empresa</th>
                            <th>Email</th>
                            <th>Fecha de Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendees as $attendee): ?>
                            <tr>
                                <td><?php echo esc_html($attendee->first_name); ?></td>
                                <td><?php echo esc_html($attendee->last_name); ?></td>
                                <td><?php echo esc_html($attendee->position); ?></td>
                                <td><?php echo esc_html($attendee->company); ?></td>
                                <td><?php echo esc_html($attendee->email); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($attendee->registration_date)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p>
                    <a href="<?php echo admin_url('admin.php?page=ecp-events&action=export&event_id=' . $event->id); ?>" class="button">Exportar Attendees a CSV</a>
                </p>
            <?php endif; ?>
        </div>
    <?php
    }

    /**
     * Página de configuración
     */
    public function settings_page()
    {
        if (isset($_POST['save_settings'])) {
            update_option('ecp_settings', $_POST['ecp_settings']);
            echo '<div class="notice notice-success"><p>Configuración guardada</p></div>';
        }

        $settings = get_option('ecp_settings', array());
    ?>
        <div class="wrap">
            <h1>Configuración del Plugin de Eventos</h1>

            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="events_per_page">Eventos por página</label></th>
                        <td><input type="number" id="events_per_page" name="ecp_settings[events_per_page]" value="<?php echo isset($settings['events_per_page']) ? $settings['events_per_page'] : 12; ?>" min="1" max="50"></td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="default_status">Estado por defecto</label></th>
                        <td>
                            <select id="default_status" name="ecp_settings[default_status]">
                                <option value="active" <?php selected(isset($settings['default_status']) ? $settings['default_status'] : 'active', 'active'); ?>>Activo</option>
                                <option value="inactive" <?php selected(isset($settings['default_status']) ? $settings['default_status'] : 'active', 'inactive'); ?>>Inactivo</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="auto_create_blog">Crear blog automáticamente</label></th>
                        <td>
                            <label>
                                <input type="checkbox" id="auto_create_blog" name="ecp_settings[auto_create_blog]" value="1" <?php checked(isset($settings['auto_create_blog']) ? $settings['auto_create_blog'] : 0, 1); ?>>
                                Crear entrada de blog automáticamente para nuevos eventos
                            </label>
                        </td>
                    </tr>
                </table>

                <?php submit_button('Guardar Configuración', 'primary', 'save_settings'); ?>
            </form>
        </div>
<?php
    }

    /**
     * Crear evento
     */
    private function create_event()
    {
        if (!wp_verify_nonce($_POST['ecp_nonce'], 'ecp_event_nonce')) {
            wp_die('Error de seguridad');
        }

        $data = array(
            'title' => sanitize_text_field($_POST['title']),
            'description' => wp_kses_post($_POST['description']),
            'event_date' => sanitize_text_field($_POST['event_date']),
            'location' => sanitize_text_field($_POST['location']),
            'registration_link' => esc_url_raw($_POST['registration_link']),
            'info_links' => sanitize_textarea_field($_POST['info_links']),
            'status' => sanitize_text_field($_POST['status'])
        );

        // Manejar subida de flyer
        if (isset($_FILES['flyer']) && $_FILES['flyer']['error'] === 0) {
            $flyer_url = $this->handle_flyer_upload($_FILES['flyer']);
            if ($flyer_url) {
                $data['flyer_url'] = $flyer_url;
            }
        }

        $event_id = ECP_Database::create_event($data);

        if ($event_id) {
            // Crear entrada de blog si se solicita
            if (isset($_POST['create_blog_post']) && $_POST['create_blog_post']) {
                $this->create_blog_post($event_id, $data);
            }

            wp_redirect(admin_url('admin.php?page=ecp-events&message=created'));
            exit;
        } else {
            wp_die('Error al crear el evento');
        }
    }

    /**
     * Actualizar evento
     */
    private function update_event()
    {
        if (!wp_verify_nonce($_POST['ecp_nonce'], 'ecp_event_nonce')) {
            wp_die('Error de seguridad');
        }

        $event_id = intval($_POST['event_id']);
        $data = array(
            'title' => sanitize_text_field($_POST['title']),
            'description' => wp_kses_post($_POST['description']),
            'event_date' => sanitize_text_field($_POST['event_date']),
            'location' => sanitize_text_field($_POST['location']),
            'registration_link' => esc_url_raw($_POST['registration_link']),
            'info_links' => sanitize_textarea_field($_POST['info_links']),
            'status' => sanitize_text_field($_POST['status'])
        );

        // Manejar subida de flyer
        if (isset($_FILES['flyer']) && $_FILES['flyer']['error'] === 0) {
            $flyer_url = $this->handle_flyer_upload($_FILES['flyer']);
            if ($flyer_url) {
                $data['flyer_url'] = $flyer_url;
            }
        }

        $result = ECP_Database::update_event($event_id, $data);

        if ($result) {
            wp_redirect(admin_url('admin.php?page=ecp-events&message=updated'));
            exit;
        } else {
            wp_die('Error al actualizar el evento');
        }
    }

    /**
     * Eliminar evento
     */
    private function delete_event()
    {
        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos para esta acción');
        }

        $event_id = intval($_GET['event_id']);
        $result = ECP_Database::delete_event($event_id);

        if ($result) {
            wp_redirect(admin_url('admin.php?page=ecp-events&message=deleted'));
            exit;
        } else {
            wp_die('Error al eliminar el evento');
        }
    }

    /**
     * Manejar subida de flyer
     */
    public function handle_flyer_upload($file)
    {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($file, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            return $movefile['url'];
        }

        return false;
    }

    /**
     * Crear entrada de blog para el evento
     */
    private function create_blog_post($event_id, $event_data)
    {
        $post_data = array(
            'post_title' => $event_data['title'],
            'post_content' => $event_data['description'],
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_author' => get_current_user_id(),
            'meta_input' => array(
                'ecp_event_id' => $event_id,
                'ecp_event_date' => $event_data['event_date'],
                'ecp_event_location' => $event_data['location'],
                'ecp_registration_link' => $event_data['registration_link']
            )
        );

        $post_id = wp_insert_post($post_data);

        if ($post_id) {
            // Actualizar el evento con el ID del post
            ECP_Database::update_event($event_id, array('blog_post_id' => $post_id));

            // Establecer imagen destacada si hay flyer
            if (isset($event_data['flyer_url']) && $event_data['flyer_url']) {
                $this->set_featured_image_from_url($post_id, $event_data['flyer_url']);
            }
        }

        return $post_id;
    }

    /**
     * Establecer imagen destacada desde URL
     */
    private function set_featured_image_from_url($post_id, $image_url)
    {
        $image_id = $this->upload_image_from_url($image_url);
        if ($image_id) {
            set_post_thumbnail($post_id, $image_id);
        }
    }

    /**
     * Subir imagen desde URL
     */
    private function upload_image_from_url($image_url)
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
     * Exportar attendees
     */
    public function export_attendees()
    {
        if (!current_user_can('manage_options')) {
            wp_die('No tienes permisos para esta acción');
        }

        $event_id = intval($_GET['event_id']);
        ECP_Database::export_attendees_csv($event_id);
    }
}
