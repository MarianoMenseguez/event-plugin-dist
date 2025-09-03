<?php

/**
 * Módulo de Divi para mostrar una tarjeta de evento específico
 */

if (!defined('ABSPATH')) {
    exit;
}

class ET_Pb_ECP_Event_Card extends ET_Builder_Module
{

    function init()
    {
        $this->name = 'Tarjeta de Evento';
        $this->slug = 'et_pb_ecp_event_card';
        $this->fb_support = true;

        $this->whitelisted_fields = array(
            'event_id',
            'show_registration',
            'show_description',
            'admin_label',
            'module_id',
            'module_class',
        );

        $this->fields_defaults = array(
            'show_registration' => array('on', 'add_default_setting'),
            'show_description' => array('on', 'add_default_setting'),
        );

        $this->main_css_element = '%%order_class%%';
        $this->advanced_options = array(
            'fonts' => array(
                'header' => array(
                    'label' => 'Header',
                    'css' => array(
                        'main' => "{$this->main_css_element} h3, {$this->main_css_element} .ecp-event-title",
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

        $fields['event_id'] = array(
            'label' => 'ID del Evento',
            'type' => 'text',
            'option_category' => 'basic_option',
            'description' => 'ID del evento a mostrar',
            'default' => ''
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

        $fields['show_description'] = array(
            'label' => 'Mostrar Descripción',
            'type' => 'yes_no_button',
            'option_category' => 'basic_option',
            'options' => array(
                'off' => 'No',
                'on' => 'Sí'
            ),
            'description' => 'Mostrar descripción del evento',
            'default' => 'on'
        );

        return $fields;
    }

    function shortcode_callback($atts, $content = null, $function_name)
    {
        $event_id = $this->shortcode_atts['event_id'];
        $show_registration = $this->shortcode_atts['show_registration'];
        $show_description = $this->shortcode_atts['show_description'];

        $module_class = ET_Builder_Element::add_module_order_class('', $function_name);

        if (empty($event_id)) {
            return '<div class="et_pb_ecp_event_card' . $module_class . '"><p>ID de evento requerido</p></div>';
        }

        $output = sprintf(
            '<div class="et_pb_ecp_event_card%1$s">%2$s</div>',
            $module_class,
            do_shortcode(sprintf(
                '[ecp_event_card event_id="%s" show_registration="%s"]',
                esc_attr($event_id),
                $show_registration === 'on' ? 'true' : 'false'
            ))
        );

        return $output;
    }
}

new ET_Pb_ECP_Event_Card;
