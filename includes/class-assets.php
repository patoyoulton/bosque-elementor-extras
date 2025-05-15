<?php
/**
 * Gestión de assets del plugin
 * 
 * @package Bosque\Elementor\Extras
 */

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase para gestionar los assets del plugin
 */
class Bosque_Extras_Assets {

    /**
     * Instancia de la clase
     * @var Bosque_Extras_Assets
     */
    private static $instance = null;
    
    /**
     * Instancia de la clase de ajustes
     * @var Bosque_Extras_Admin_Settings
     */
    private $settings;

    /**
     * Obtener instancia de la clase
     * @return Bosque_Extras_Assets
     */
    public static function get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Obtener instancia de ajustes
        $this->settings = Bosque_Extras_Admin_Settings::get_instance();
        
        // Verificar si Elementor está cargado
        if (!did_action('elementor/loaded')) {
            return;
        }
        
        // Registrar estilos y scripts
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'register_assets']);
        add_action('elementor/editor/after_enqueue_styles', [$this, 'register_assets']);
        
        // Registrar estilos de admin
        add_action('admin_enqueue_scripts', [$this, 'register_admin_assets']);
    }

    /**
     * Registrar estilos y scripts para el frontend
     */
    public function register_assets() {
        // Registrar y cargar estilos solo para las funcionalidades habilitadas
        if ($this->settings->is_feature_enabled('breadcrumb')) {
            // Registrar estilos para breadcrumb
            wp_register_style(
                'bosque-breadcrumb',
                BOSQUE_EXTRAS_URL . 'assets/css/bosque-breadcrumb.css',
                [],
                BOSQUE_EXTRAS_VERSION
            );
            
            // Encolar estilos de breadcrumb
            wp_enqueue_style('bosque-breadcrumb');
        }
        
        // Cargar estilos de Contact Form 7 Styler si está habilitado
        if ($this->settings->is_feature_enabled('cf7') && class_exists('WPCF7')) {
            // Registrar estilos para Contact Form 7
            wp_register_style(
                'bosque-cf7-styler',
                BOSQUE_EXTRAS_URL . 'assets/css/bosque-cf7-styler.css',
                [],
                BOSQUE_EXTRAS_VERSION
            );
            
            // Encolar estilos de Contact Form 7
            wp_enqueue_style('bosque-cf7-styler');
        }
        
        // Aquí se pueden añadir más recursos para futuras funcionalidades
    }
    
    /**
     * Registrar estilos y scripts para el admin
     * 
     * @param string $hook Página actual del admin
     */
    public function register_admin_assets($hook) {
        // Cargar estilos solo en la página de ajustes del plugin
        if (strpos($hook, 'bosque-elementor-extras') !== false) {
            wp_enqueue_style(
                'bosque-admin-styles',
                BOSQUE_EXTRAS_URL . 'assets/css/admin-styles.css',
                [],
                BOSQUE_EXTRAS_VERSION
            );
        }
    }
}
