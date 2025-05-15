<?php
/**
 * Plugin Name: Bosque Elementor Extras
 * Description: Widgets adicionales para Elementor con panel de configuración
 * Version: 1.0.0
 * Author: Bosquestudio.cl
 * Author URI: https://bosquestudio.cl
 * Text Domain: bosque-elementor-extras
 */

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('BOSQUE_EXTRAS_VERSION', '1.0.0');
define('BOSQUE_EXTRAS_FILE', __FILE__);
define('BOSQUE_EXTRAS_PATH', plugin_dir_path(__FILE__));
define('BOSQUE_EXTRAS_URL', plugin_dir_url(__FILE__));
define('BOSQUE_EXTRAS_SLUG', 'bosque-elementor-extras');

/**
 * Clase principal del plugin
 */
class Bosque_Elementor_Extras {

    /**
     * Instancia de la clase
     * @var Bosque_Elementor_Extras
     */
    private static $instance = null;
    
    /**
     * Instancia de la clase de ajustes
     * @var Bosque_Extras_Admin_Settings
     */
    private $settings;

    /**
     * Obtener instancia de la clase
     * @return Bosque_Elementor_Extras
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
        // Verificar si Elementor está activo
        add_action('plugins_loaded', [$this, 'init']);
        
        // Activar/desactivar hooks
        register_activation_hook(BOSQUE_EXTRAS_FILE, [$this, 'activate']);
        register_deactivation_hook(BOSQUE_EXTRAS_FILE, [$this, 'deactivate']);
    }
    
    /**
     * Activar plugin
     */
    public function activate() {
        // Crear opciones por defecto si no existen
        if (!get_option('bosque_elementor_extras_options')) {
            $default_options = [
                'breadcrumb_enabled' => 'yes',
                // Aquí se pueden añadir más opciones para futuras funcionalidades
            ];
            update_option('bosque_elementor_extras_options', $default_options);
        }
        
        // Limpiar caché de permalinks
        flush_rewrite_rules();
    }
    
    /**
     * Desactivar plugin
     */
    public function deactivate() {
        // Limpiar caché de permalinks
        flush_rewrite_rules();
    }

    /**
     * Inicializar plugin
     */
    public function init() {
        // Verificar si Elementor está instalado y activado
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }

        // Cargar archivos
        $this->includes();
        
        // Obtener instancia de ajustes
        $this->settings = Bosque_Extras_Admin_Settings::get_instance();

        // Registrar categoría de widget
        add_action('elementor/elements/categories_registered', [$this, 'register_widget_category']);

        // Registrar widgets (compatible con Elementor 3.x)
        if (defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '3.0.0', '<')) {
            // Versiones anteriores de Elementor
            add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
        } else {
            // Elementor 3.0 y superior
            add_action('elementor/widgets/register', [$this, 'register_widgets']);
        }

        // Cargar traducciones
        add_action('init', [$this, 'load_textdomain']);
    }

    /**
     * Incluir archivos necesarios
     */
    public function includes() {
        // Clases principales
        require_once BOSQUE_EXTRAS_PATH . 'includes/class-admin-settings.php';
        require_once BOSQUE_EXTRAS_PATH . 'includes/class-assets.php';
        require_once BOSQUE_EXTRAS_PATH . 'includes/class-breadcrumb-generator.php';
        
        // Inicializar assets
        Bosque_Extras_Assets::get_instance();
        
        // Los widgets se cargarán más tarde, cuando Elementor esté completamente inicializado
        add_action('elementor/widgets/widgets_registered', [$this, 'load_widgets']);
        add_action('elementor/widgets/register', [$this, 'load_widgets']);
    }
    
    /**
     * Cargar archivos de widgets
     */
    public function load_widgets() {
        // Cargar widgets solo cuando Elementor esté completamente inicializado
        if (!class_exists('\Elementor\Widget_Base')) {
            return;
        }
        
        // Cargar widget de breadcrumb si está habilitado
        if ($this->settings->is_feature_enabled('breadcrumb')) {
            require_once BOSQUE_EXTRAS_PATH . 'widgets/class-breadcrumb-widget.php';
        }
        
        // Cargar widget de Contact Form 7 si está habilitado y CF7 está disponible
        if ($this->settings->is_feature_enabled('cf7') && class_exists('WPCF7')) {
            require_once BOSQUE_EXTRAS_PATH . 'widgets/class-cf7-widget.php';
        }
    }

    /**
     * Registrar categoría de widget
     *
     * @param \Elementor\Elements_Manager $elements_manager
     */
    public function register_widget_category($elements_manager) {
        $elements_manager->add_category(
            'bosque-elements',
            [
                'title' => __('Bosque Elements', 'bosque-elementor-extras'),
                'icon' => 'fa fa-tree',
            ]
        );
    }

    /**
     * Registrar widgets
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Widget manager de Elementor
     */
    public function register_widgets($widgets_manager) {
        // Asegurarnos de que Elementor esté completamente cargado antes de incluir nuestros widgets
        if (!class_exists('\Elementor\Widget_Base')) {
            return;
        }
        
        // Cargar los widgets primero
        $this->load_widgets();
        
        // Verificar si el breadcrumb está habilitado y la clase existe
        if ($this->settings->is_feature_enabled('breadcrumb') && class_exists('\Bosque\Elementor\Widgets\Breadcrumb_Widget')) {
            if (defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '3.0.0', '<')) {
                // Versiones anteriores de Elementor
                \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Bosque\Elementor\Widgets\Breadcrumb_Widget());
            } else {
                // Elementor 3.0 y superior
                $widgets_manager->register(new \Bosque\Elementor\Widgets\Breadcrumb_Widget());
            }
        }
        
        // Verificar si Contact Form 7 está habilitado y la clase existe
        if ($this->settings->is_feature_enabled('cf7') && class_exists('\Bosque\Elementor\Widgets\CF7_Widget')) {
            if (defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '3.0.0', '<')) {
                // Versiones anteriores de Elementor
                \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Bosque\Elementor\Widgets\CF7_Widget());
            } else {
                // Elementor 3.0 y superior
                $widgets_manager->register(new \Bosque\Elementor\Widgets\CF7_Widget());
            }
        }
        
        // Aquí se pueden registrar más widgets en el futuro
    }

    /**
     * Cargar traducciones
     */
    public function load_textdomain() {
        load_plugin_textdomain('bosque-elementor-extras', false, dirname(plugin_basename(BOSQUE_EXTRAS_FILE)) . '/languages');
    }

    /**
     * Mostrar notificación si Elementor no está instalado
     */
    public function admin_notice_missing_elementor() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            __('"%1$s" requiere "%2$s" para funcionar correctamente. Por favor, instala y activa Elementor primero.', 'bosque-elementor-extras'),
            '<strong>Bosque Elementor Extras</strong>',
            '<strong>Elementor</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}

// Inicializar el plugin
Bosque_Elementor_Extras::get_instance();
