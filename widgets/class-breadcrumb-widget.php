<?php
/**
 * Widget de Breadcrumb para Elementor
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
function breadcrumb_widget_can_load() {
    // Verificar si Elementor está cargado
    if (!did_action('elementor/loaded')) {
        return false;
    }
    
    // Verificar si las clases de Elementor están disponibles
    if (!class_exists('\Elementor\Widget_Base')) {
        return false;
    }
    
    return true;
}

// No cargar si las dependencias no están disponibles
if (!breadcrumb_widget_can_load()) {
    return;
}

// Importar clases de Elementor
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

/**
 * Widget de Breadcrumb para Elementor
 */
class Breadcrumb_Widget extends Widget_Base {

    /**
     * Obtener nombre del widget
     * 
     * @return string
     */
    public function get_name() {
        return 'bosque_breadcrumb';
    }

    /**
     * Obtener título del widget
     * 
     * @return string
     */
    public function get_title() {
        return __('Bosque Breadcrumb', 'bosque-elementor-breadcrumb');
    }

    /**
     * Obtener ícono del widget
     * 
     * @return string
     */
    public function get_icon() {
        return 'eicon-navigation-horizontal';
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
        return ['breadcrumb', 'navegación', 'migas', 'ruta', 'bosque'];
    }

    /**
     * Registrar controles del widget
     */
    protected function register_controls() {
        // Sección de contenido
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Contenido', 'bosque-elementor-breadcrumb'),
            ]
        );

        $this->add_control(
            'home_title',
            [
                'label' => __('Título de inicio', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Inicio', 'bosque-elementor-breadcrumb'),
            ]
        );

        $this->add_control(
            'separator',
            [
                'label' => __('Separador', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::TEXT,
                'default' => '›',
            ]
        );

        $this->add_control(
            'show_home',
            [
                'label' => __('Mostrar inicio', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Sí', 'bosque-elementor-breadcrumb'),
                'label_off' => __('No', 'bosque-elementor-breadcrumb'),
            ]
        );

        $this->add_control(
            'show_current',
            [
                'label' => __('Mostrar página actual', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Sí', 'bosque-elementor-breadcrumb'),
                'label_off' => __('No', 'bosque-elementor-breadcrumb'),
            ]
        );

        $this->add_control(
            'show_on_home',
            [
                'label' => __('Mostrar en página de inicio', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => __('Sí', 'bosque-elementor-breadcrumb'),
                'label_off' => __('No', 'bosque-elementor-breadcrumb'),
            ]
        );

        $this->end_controls_section();

        // Sección de estilo del contenedor
        $this->start_controls_section(
            'section_container_style',
            [
                'label' => __('Contenedor', 'bosque-elementor-breadcrumb'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __('Alineación', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __('Izquierda', 'bosque-elementor-breadcrumb'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Centro', 'bosque-elementor-breadcrumb'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('Derecha', 'bosque-elementor-breadcrumb'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb' => 'justify-content: {{VALUE}};',
                ],
                'default' => 'flex-start',
            ]
        );

        $this->add_control(
            'container_bg_color',
            [
                'label' => __('Color de fondo', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .bosque-breadcrumb',
            ]
        );

        $this->add_responsive_control(
            'container_border_radius',
            [
                'label' => __('Radio de borde', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => __('Padding', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default' => [
                    'top' => '10',
                    'right' => '10',
                    'bottom' => '10',
                    'left' => '10',
                    'unit' => 'px',
                    'isLinked' => true,
                ],
            ]
        );

        $this->add_responsive_control(
            'container_margin',
            [
                'label' => __('Margin', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .bosque-breadcrumb',
            ]
        );

        $this->end_controls_section();

        // Sección de paleta de colores
        $this->start_controls_section(
            'section_color_palette',
            [
                'label' => __('Paleta de colores', 'bosque-elementor-extras'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'primary_color',
            [
                'label' => __('Color primario', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb-link' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bosque-breadcrumb-separator' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'secondary_color',
            [
                'label' => __('Color secundario', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb-current' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => __('Color hover', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb-link:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => __('Color de fondo', 'bosque-elementor-extras'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Sección de estilo de los elementos
        $this->start_controls_section(
            'section_items_style',
            [
                'label' => __('Elementos', 'bosque-elementor-breadcrumb'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .bosque-breadcrumb-item',
            ]
        );

        $this->add_responsive_control(
            'item_spacing',
            [
                'label' => __('Espacio entre elementos', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb-separator' => 'margin: 0 {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('tabs_items_style');

        // Pestaña normal
        $this->start_controls_tab(
            'tab_item_normal',
            [
                'label' => __('Normal', 'bosque-elementor-breadcrumb'),
            ]
        );

        $this->add_control(
            'item_color',
            [
                'label' => __('Color de texto', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb-link' => 'color: {{VALUE}};',
                ],
                'default' => '#333333',
            ]
        );

        $this->add_control(
            'separator_color',
            [
                'label' => __('Color del separador', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb-separator' => 'color: {{VALUE}};',
                ],
                'default' => '#666666',
            ]
        );

        $this->end_controls_tab();

        // Pestaña hover
        $this->start_controls_tab(
            'tab_item_hover',
            [
                'label' => __('Hover', 'bosque-elementor-breadcrumb'),
            ]
        );

        $this->add_control(
            'item_hover_color',
            [
                'label' => __('Color de texto', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb-link:hover' => 'color: {{VALUE}};',
                ],
                'default' => '#000000',
            ]
        );

        $this->end_controls_tab();

        // Pestaña actual
        $this->start_controls_tab(
            'tab_item_current',
            [
                'label' => __('Actual', 'bosque-elementor-breadcrumb'),
            ]
        );

        $this->add_control(
            'item_current_color',
            [
                'label' => __('Color de texto', 'bosque-elementor-breadcrumb'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bosque-breadcrumb-current' => 'color: {{VALUE}};',
                ],
                'default' => '#000000',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    /**
     * Renderizar el widget
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $options = [
            'home_title'     => $settings['home_title'],
            'separator'      => $settings['separator'],
            'show_home'      => 'yes' === $settings['show_home'],
            'show_current'   => 'yes' === $settings['show_current'],
            'show_on_home'   => 'yes' === $settings['show_on_home'],
        ];
        
        $breadcrumb_generator = new \Breadcrumb_Generator($options);
        
        echo $breadcrumb_generator->generate();
    }
    
    /**
     * Renderizar el contenido en el editor
     */
    protected function content_template() {
        ?>
        <nav class="bosque-breadcrumb-container">
            <ul class="bosque-breadcrumb">
                <# 
                // Mostrar una previsualización estática del breadcrumb
                var homeText = settings.home_text || '<?php echo __('Inicio', 'bosque-elementor-extras'); ?>';
                var separator = settings.separator || '/';
                var showHome = settings.show_home;
                var showCurrent = settings.show_current;
                #>
                
                <# if (showHome === 'yes') { #>
                    <li class="bosque-breadcrumb-item">
                        <a href="#" class="bosque-breadcrumb-link">{{{ homeText }}}</a>
                        <span class="bosque-breadcrumb-separator">{{{ separator }}}</span>
                    </li>
                <# } #>
                
                <# if (settings.show_parent === 'yes') { #>
                    <li class="bosque-breadcrumb-item">
                        <a href="#" class="bosque-breadcrumb-link"><?php echo __('Categoría Ejemplo', 'bosque-elementor-extras'); ?></a>
                        <span class="bosque-breadcrumb-separator">{{{ separator }}}</span>
                    </li>
                <# } #>
                
                <# if (showCurrent === 'yes') { #>
                    <li class="bosque-breadcrumb-item bosque-breadcrumb-current">
                        <?php echo __('Página Actual', 'bosque-elementor-extras'); ?>
                    </li>
                <# } #>
            </ul>
            <div class="bosque-breadcrumb-editor-note">
                <?php echo __('Esta es una previsualización. El breadcrumb real se mostrará en el frontend.', 'bosque-elementor-extras'); ?>
            </div>
        </nav>
        <?php
    }
}
