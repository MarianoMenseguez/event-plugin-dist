<?php

/**
 * Módulo de Divi para mostrar lista de eventos
 */

if (!defined('ABSPATH')) {
    exit;
}

class ET_Pb_ECP_Events extends ET_Builder_Module
{

    function init()
    {
        $this->name = 'Lista de Eventos';
        $this->slug = 'et_pb_ecp_events';
        $this->fb_support = true;

        $this->whitelisted_fields = array(
            'events_limit',
            'show_registration',
            'layout_style',
            'columns',
            'future_only',
            'show_past',
            'admin_label',
            'module_id',
            'module_class',
        );

        $this->fields_defaults = array(
            'events_limit' => array('6', 'add_default_setting'),
            'show_registration' => array('on', 'add_default_setting'),
            'layout_style' => array('grid', 'add_default_setting'),
            'columns' => array('3', 'add_default_setting'),
            'future_only' => array('on', 'add_default_setting'),
            'show_past' => array('off', 'add_default_setting'),
        );

        $this->main_css_element = '%%order_class%%';
        $this->advanced_options = array(
            'fonts' => array(
                'header' => array(
                    'label' => 'Header',
                    'css' => array(
                        'main' => "{$this->main_css_element} h2, {$this->main_css_element} h3",
                    ),
                ),
                'body' => array(
                    'label' => 'Body',
                    'css' => array(
                        'main' => "{$this->main_css_element} p, {$this->main_css_element} .ecp-event-description",
                    ),
                ),
            ),
            'background' => array(),
            'border' => array(),
            'custom_margin_padding' => array(
                'css' => array(
                    'important' => 'all',
                ),
            ),
        );
    }

    function get_fields()
    {
        $fields = array();

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

        $fields['columns'] = array(
            'label' => 'Columnas',
            'type' => 'select',
            'option_category' => 'basic_option',
            'options' => array(
                '1' => '1 Columna',
                '2' => '2 Columnas',
                '3' => '3 Columnas',
                '4' => '4 Columnas'
            ),
            'description' => 'Número de columnas para el layout grid',
            'default' => '3'
        );

        $fields['future_only'] = array(
            'label' => 'Solo Eventos Futuros',
            'type' => 'yes_no_button',
            'option_category' => 'basic_option',
            'options' => array(
                'off' => 'No',
                'on' => 'Sí'
            ),
            'description' => 'Mostrar solo eventos futuros',
            'default' => 'on'
        );

        $fields['show_past'] = array(
            'label' => 'Incluir Eventos Pasados',
            'type' => 'yes_no_button',
            'option_category' => 'basic_option',
            'options' => array(
                'off' => 'No',
                'on' => 'Sí'
            ),
            'description' => 'Incluir eventos pasados en la lista',
            'default' => 'off'
        );

        return $fields;
    }

    function shortcode_callback($atts, $content = null, $function_name)
    {
        $events_limit = $this->shortcode_atts['events_limit'];
        $show_registration = $this->shortcode_atts['show_registration'];
        $layout_style = $this->shortcode_atts['layout_style'];
        $columns = $this->shortcode_atts['columns'];
        $future_only = $this->shortcode_atts['future_only'];
        $show_past = $this->shortcode_atts['show_past'];

        $module_class = ET_Builder_Element::add_module_order_class('', $function_name);

        $output = sprintf(
            '<div class="et_pb_ecp_events%1$s" data-layout="%2$s" data-columns="%3$s">%4$s</div>',
            $module_class,
            esc_attr($layout_style),
            esc_attr($columns),
            do_shortcode(sprintf(
                '[ecp_events_list limit="%s" show_registration="%s" layout="%s" columns="%s" future_only="%s" show_past="%s"]',
                esc_attr($events_limit),
                $show_registration === 'on' ? 'true' : 'false',
                esc_attr($layout_style),
                esc_attr($columns),
                $future_only === 'on' ? 'true' : 'false',
                $show_past === 'on' ? 'true' : 'false'
            ))
        );

        return $output;
    }
}

new ET_Pb_ECP_Events;
