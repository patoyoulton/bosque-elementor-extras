<?php
/**
 * Widget de Contact Form 7 para Elementor
 * 
 * @package Bosque\Elementor\Extras
 */

namespace Bosque\Elementor\Widgets;

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Verificar si podemos cargar el widget
 * 
 * @return bool
 */
function cf7_widget_can_load() {
    // Verificar si Elementor está cargado
    if (!did_action('elementor/loaded')) {
        return false;
    }
    
    // Verificar si las clases de Elementor están disponibles
    if (!class_exists('\Elementor\Widget_Base')) {
        return false;
    }
    
    // Verificar si Contact Form 7 está disponible
    if (!class_exists('WPCF7')) {
        return false;
    }
    
    return true;
}

// No cargar si las dependencias no están disponibles
if (!cf7_widget_can_load()) {
    return;
}

// Importar clases de Elementor
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;

/**
 * Widget de Contact Form 7 para Elementor
 */
class CF7_Widget extends Widget_Base {

    /**
     * Obtener nombre del widget
     * 
     * @return string
     */
    public function get_name() {
        return 'bosque_cf7_styler';
    }

    /**
     * Obtener título del widget
     * 
     * @return string
     */
    public function get_title() {
        return __('Bosque CF7 Styler', 'bosque-elementor-extras');
    }

    /**
     * Obtener ícono del widget
     * 
     * @return string
     */
    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    /**
     * Obtener categorías del widget
     * 
     * @return array
     */
    public function get_categories() {
        return ['bosque-elements'];
    }

    /**
     * Obtener palabras clave del widget
     * 
     * @return array
     */
    public function get_keywords() {
        return ['formulario', 'contacto', 'cf7', 'contact form 7', 'bosque'];
    }

    /**
     * Registrar controles del widget
     */
    protected function register_controls() {
        // Sección de contenido
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Contenido', 'bosque-elementor-extras'),
            ]
        );

        // Obtener formularios de Contact Form 7
        $cf7_forms = $this->get_cf7_forms();

        $this->add_control(
            'cf7_form',
            [
                'label' => __('Seleccionar formulario', 'bosque-elementor-extras'),
                'type' => Controls_Manager::SELECT,
                'options' => $cf7_forms,
                'default' => '',
                'description' => __('Selecciona un formulario de Contact Form 7', 'bosque-elementor-extras'),
            ]
        );

        $this->add_control(
            'form_title',
            [
                'label' => __('Título del formulario', 'bosque-elementor-extras'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Ingresa el título del formulario', 'bosque-elementor-extras'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'form_description',
            [
                'label' => __('Descripción del formulario', 'bosque-elementor-extras'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => '',
                'placeholder' => __('Ingresa la descripción del formulario', 'bosque-elementor-extras'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => __('Mostrar título', 'bosque-elementor-extras'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Sí', 'bosque-elementor-extras'),
                'label_off' => __('No', 'bosque-elementor-extras'),
            ]
        );

        $this->add_control(
            'show_description',
            [
                'label' => __('Mostrar descripción', 'bosque-elementor-extras'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Sí', 'bosque-elementor-extras'),
                'label_off' => __('No', 'bosque-elementor-extras'),
            ]
        );

        $this->end_controls_section();

        // Sección de estilo del contenedor
        $this->start_controls_section(
            'section_container_style',
            [
                'label' => __('Contenedor', 'bosque-elementor-extras'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'form_width',
            [
                'label' => __('Ancho del formulario', 'bosque-elementor-extras'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'container_bg_color',
            [
                'label' => __('Color de fondo', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .bosque-cf7-container',
            ]
        );

        $this->add_responsive_control(
            'container_border_radius',
            [
                'label' => __('Radio de borde', 'bosque-elementor-extras'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => __('Padding', 'bosque-elementor-extras'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '20',
                    'right' => '20',
                    'bottom' => '20',
                    'left' => '20',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .bosque-cf7-container',
            ]
        );

        $this->end_controls_section();

        // Sección de estilo del título y descripción
        $this->start_controls_section(
            'section_title_style',
            [
                'label' => __('Título y Descripción', 'bosque-elementor-extras'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color del título', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-title' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .bosque-cf7-title',
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __('Margen del título', 'bosque-elementor-extras'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Color de la descripción', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-description' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_description' => 'yes',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .bosque-cf7-description',
                'condition' => [
                    'show_description' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'description_margin',
            [
                'label' => __('Margen de la descripción', 'bosque-elementor-extras'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'show_description' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Sección de estilo de los campos
        $this->start_controls_section(
            'section_fields_style',
            [
                'label' => __('Campos', 'bosque-elementor-extras'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'field_text_color',
            [
                'label' => __('Color de texto', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-acceptance)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'field_bg_color',
            [
                'label' => __('Color de fondo', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-acceptance)' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'placeholder_color',
            [
                'label' => __('Color de placeholder', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-acceptance)::placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-acceptance)::-webkit-input-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-acceptance)::-moz-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-acceptance):-ms-input-placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'field_typography',
                'selector' => '{{WRAPPER}} .bosque-cf7-container .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-acceptance)',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'field_border',
                'selector' => '{{WRAPPER}} .bosque-cf7-container .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-acceptance)',
            ]
        );

        $this->add_responsive_control(
            'field_border_radius',
            [
                'label' => __('Radio de borde', 'bosque-elementor-extras'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-acceptance)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_padding',
            [
                'label' => __('Padding', 'bosque-elementor-extras'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-acceptance)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_margin',
            [
                'label' => __('Margen', 'bosque-elementor-extras'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-form-control:not(.wpcf7-submit):not(.wpcf7-checkbox):not(.wpcf7-radio):not(.wpcf7-acceptance)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Sección de posicionamiento del botón
        $this->start_controls_section(
            'section_button_position',
            [
                'label' => __('Posicionamiento del botón', 'bosque-elementor-extras'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
            'button_alignment',
            [
                'label' => __('Alineación', 'bosque-elementor-extras'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Izquierda', 'bosque-elementor-extras'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Centro', 'bosque-elementor-extras'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Derecha', 'bosque-elementor-extras'),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justificado', 'bosque-elementor-extras'),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-form p:last-child' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'button_position_width',
            [
                'label' => __('Ancho del botón', 'bosque-elementor-extras'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'button_position_margin',
            [
                'label' => __('Margen', 'bosque-elementor-extras'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        // Sección de estilo del botón
        $this->start_controls_section(
            'section_button_style',
            [
                'label' => __('Estilo del botón', 'bosque-elementor-extras'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_button_style');

        // Pestaña normal
        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Normal', 'bosque-elementor-extras'),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Color de texto', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => __('Color de fondo', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        // Pestaña hover
        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __('Hover', 'bosque-elementor-extras'),
            ]
        );

        $this->add_control(
            'button_text_hover_color',
            [
                'label' => __('Color de texto', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_hover_color',
            [
                'label' => __('Color de fondo', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit',
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => __('Radio de borde', 'bosque-elementor-extras'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'bosque-elementor-extras'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_style_margin',
            [
                'label' => __('Margen', 'bosque-elementor-extras'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_style_width',
            [
                'label' => __('Ancho', 'bosque-elementor-extras'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bosque-cf7-container .wpcf7-submit' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Obtener formularios de Contact Form 7
     * 
     * @return array
     */
    private function get_cf7_forms() {
        $forms = [];

        // Verificar si Contact Form 7 está activo usando una función común
        if (class_exists('WPCF7_ContactForm')) {
            // Obtener todos los formularios de Contact Form 7
            $args = array(
                'post_type' => 'wpcf7_contact_form',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
            );
            
            $cf7_forms = get_posts($args);

            if (!empty($cf7_forms)) {
                $forms[''] = __('Seleccionar un formulario', 'bosque-elementor-extras');
                
                foreach ($cf7_forms as $form) {
                    // Usar el ID como clave y el título como valor
                    $forms[$form->ID] = $form->post_title;
                }
            } else {
                $forms[''] = __('No hay formularios disponibles', 'bosque-elementor-extras');
            }
        } else {
            $forms[''] = __('Contact Form 7 no está instalado', 'bosque-elementor-extras');
        }

        return $forms;
    }

    /**
     * Renderizar el widget
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Verificar si Contact Form 7 está activo
        if (!function_exists('wpcf7')) {
            echo '<div class="bosque-cf7-container">';
            echo __('Contact Form 7 no está instalado o activado', 'bosque-elementor-extras');
            echo '</div>';
            return;
        }
        
        // Verificar si se ha seleccionado un formulario
        if (empty($settings['cf7_form'])) {
            echo '<div class="bosque-cf7-container">';
            echo __('Por favor, selecciona un formulario de Contact Form 7', 'bosque-elementor-extras');
            echo '</div>';
            return;
        }
        
        $form_id = $settings['cf7_form'];
        $form_title = $settings['form_title'];
        $form_description = $settings['form_description'];
        $show_title = $settings['show_title'];
        $show_description = $settings['show_description'];
        
        // Verificar si el formulario existe de manera más simple
        // Evitamos usar wpcf7_contact_form() directamente para prevenir errores
        $cf7_posts = get_posts([
            'post_type' => 'wpcf7_contact_form',
            'p' => $form_id,
            'posts_per_page' => 1,
        ]);
        
        if (empty($cf7_posts)) {
            echo '<div class="bosque-cf7-container">';
            echo __('El formulario seleccionado no existe o ha sido eliminado', 'bosque-elementor-extras');
            echo '</div>';
            return;
        }
        
        echo '<div class="bosque-cf7-container">';
        
        if ('yes' === $show_title && !empty($form_title)) {
            echo '<h3 class="bosque-cf7-title">' . esc_html($form_title) . '</h3>';
        }
        
        if ('yes' === $show_description && !empty($form_description)) {
            echo '<div class="bosque-cf7-description">' . wp_kses_post($form_description) . '</div>';
        }
        
        // Renderizar el formulario usando el método más simple y confiable
        // Usamos directamente el shortcode sin verificaciones adicionales
        echo do_shortcode('[contact-form-7 id="' . esc_attr($form_id) . '"]');
        
        echo '</div>';
    }
    
    /**
     * Renderizar el contenido en el editor
     */
    protected function content_template() {
        ?>
        <div class="bosque-cf7-container bosque-cf7-editor-preview">
            <# if ( settings.show_title && settings.form_title ) { #>
                <h3 class="bosque-cf7-title">{{{ settings.form_title }}}</h3>
            <# } #>
            
            <# if ( settings.show_description && settings.form_description ) { #>
                <div class="bosque-cf7-description">{{{ settings.form_description }}}</div>
            <# } #>
            
            <div class="bosque-cf7-form-preview">
                <# if ( settings.cf7_form ) { #>
                    <!-- Simulación del formulario para previsualización -->
                    <div class="bosque-cf7-form-fields">
                        <div class="bosque-cf7-form-field">
                            <label><?php echo __('Nombre', 'bosque-elementor-extras'); ?></label>
                            <input type="text" class="wpcf7-form-control" placeholder="<?php echo __('Nombre', 'bosque-elementor-extras'); ?>">
                        </div>
                        
                        <div class="bosque-cf7-form-field">
                            <label><?php echo __('Email', 'bosque-elementor-extras'); ?></label>
                            <input type="email" class="wpcf7-form-control" placeholder="<?php echo __('Email', 'bosque-elementor-extras'); ?>">
                        </div>
                        
                        <div class="bosque-cf7-form-field">
                            <label><?php echo __('Mensaje', 'bosque-elementor-extras'); ?></label>
                            <textarea class="wpcf7-form-control" placeholder="<?php echo __('Mensaje', 'bosque-elementor-extras'); ?>"></textarea>
                        </div>
                        
                        <div class="bosque-cf7-form-field">
                            <button type="button" class="wpcf7-form-control wpcf7-submit"><?php echo __('Enviar', 'bosque-elementor-extras'); ?></button>
                        </div>
                    </div>
                    <div class="bosque-cf7-editor-note">
                        <?php echo __('Esta es una previsualización. El formulario real se mostrará en el frontend.', 'bosque-elementor-extras'); ?>
                    </div>
                <# } else { #>
                    <div class="bosque-cf7-editor-placeholder">
                        <?php echo __('Por favor, selecciona un formulario de Contact Form 7', 'bosque-elementor-extras'); ?>
                    </div>
                <# } #>
            </div>
        </div>
        <?php
    }
}
