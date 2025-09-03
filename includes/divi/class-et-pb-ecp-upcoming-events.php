<?php

/**
 * Módulo de Divi para mostrar próximos eventos
 */

if (!defined('ABSPATH')) {
    exit;
}

class ET_Pb_ECP_Upcoming_Events extends ET_Builder_Module
{

    function init()
    {
        $this->name = 'Próximos Eventos';
        $this->slug = 'et_pb_ecp_upcoming_events';
        $this->fb_support = true;

        $this->whitelisted_fields = array(
            'events_limit',
            'show_registration',
            'layout_style',
            'admin_label',
            'module_id',
            'module_class',
        );

        $this->fields_defaults = array(
            'events_limit' => array('3', 'add_default_setting'),
            'show_registration' => array('on', 'add_default_setting'),
            'layout_style' => array('horizontal', 'add_default_setting'),
        );

        $this->main_css_element = '%%order_class%%';
        $this->advanced_options = array(
            'fonts' => array(
                'header' => array(
                    'label' => 'Header',
                    'css' => array(
                        'main' => "{$this->main_css_element} h3, {$this->main_css_element} h4",
                    ),
                ),
                'body' => array(
                    'label' => 'Body',
                    'css' => array(
                        'main' => "{$this->main_css_element} p, {$this->main_css_element} .ecp-event-excerpt",
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
            'description' => 'Número de eventos próximos a mostrar',
            'default' => '3'
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
                'horizontal' => 'Horizontal',
                'vertical' => 'Vertical',
                'grid' => 'Grid'
            ),
            'description' => 'Estilo de presentación de los eventos',
            'default' => 'horizontal'
        );

        return $fields;
    }

    function shortcode_callback($atts, $content = null, $function_name)
    {
        $events_limit = $this->shortcode_atts['events_limit'];
        $show_registration = $this->shortcode_atts['show_registration'];
        $layout_style = $this->shortcode_atts['layout_style'];

        $module_class = ET_Builder_Element::add_module_order_class('', $function_name);

        $output = sprintf(
            '<div class="et_pb_ecp_upcoming_events%1$s" data-layout="%2$s">%3$s</div>',
            $module_class,
            esc_attr($layout_style),
            do_shortcode(sprintf(
                '[ecp_upcoming_events limit="%s" show_registration="%s" layout="%s"]',
                esc_attr($events_limit),
                $show_registration === 'on' ? 'true' : 'false',
                esc_attr($layout_style)
            ))
        );

        return $output;
    }
}

new ET_Pb_ECP_Upcoming_Events;
