<?php
/**
 * Clase para manejar el panel de ajustes del plugin
 * 
 * @package Bosque\Elementor\Extras
 */

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase para manejar el panel de ajustes del plugin
 */
class Bosque_Extras_Admin_Settings {

    /**
     * Instancia de la clase
     * @var Bosque_Extras_Admin_Settings
     */
    private static $instance = null;

    /**
     * Opciones del plugin
     * @var array
     */
    private $options;

    /**
     * Obtener instancia de la clase
     * @return Bosque_Extras_Admin_Settings
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
        // Cargar opciones
        $this->options = get_option('bosque_elementor_extras_options', $this->get_default_options());

        // Registrar hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Obtener opciones por defecto
     * @return array
     */
    public function get_default_options() {
        return array(
            'breadcrumb_enabled' => 'yes',
            'cf7_enabled' => 'yes',
            // Aquí se pueden añadir más opciones para futuras funcionalidades
        );
    }

    /**
     * Obtener una opción específica
     * 
     * @param string $key Clave de la opción
     * @param mixed $default Valor por defecto si la opción no existe
     * @return mixed
     */
    public function get_option($key, $default = '') {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }
        return $default;
    }

    /**
     * Verificar si una funcionalidad está habilitada
     * 
     * @param string $feature Nombre de la funcionalidad
     * @return boolean
     */
    public function is_feature_enabled($feature) {
        // Para funcionalidades habilitadas por defecto
        if ($feature === 'breadcrumb' || $feature === 'cf7') {
            $option_key = $feature . '_enabled';
            return $this->get_option($option_key, 'yes') === 'yes';
        }
        
        // Para otras funcionalidades
        $option_key = $feature . '_enabled';
        return $this->get_option($option_key, 'no') === 'yes';
    }

    /**
     * Añadir menú de administración
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Bosque Elementor Extras', 'bosque-elementor-extras'),
            __('Bosque Extras', 'bosque-elementor-extras'),
            'manage_options',
            'bosque-elementor-extras',
            array($this, 'render_settings_page'),
            'dashicons-layout',
            100
        );
    }

    /**
     * Registrar configuraciones
     */
    public function register_settings() {
        register_setting(
            'bosque_elementor_extras_options_group',
            'bosque_elementor_extras_options',
            array($this, 'sanitize_options')
        );

        // Sección de funcionalidades
        add_settings_section(
            'bosque_elementor_extras_features_section',
            __('Funcionalidades', 'bosque-elementor-extras'),
            array($this, 'render_features_section'),
            'bosque-elementor-extras'
        );

        // Campo para activar/desactivar breadcrumb
        add_settings_field(
            'breadcrumb_enabled',
            __('Breadcrumb para Elementor', 'bosque-elementor-extras'),
            array($this, 'render_checkbox_field'),
            'bosque-elementor-extras',
            'bosque_elementor_extras_features_section',
            array(
                'id' => 'breadcrumb_enabled',
                'label' => __('Habilitar widget de Breadcrumb', 'bosque-elementor-extras'),
                'description' => __('Añade un widget de migas de pan (breadcrumb) a Elementor.', 'bosque-elementor-extras')
            )
        );
        
        // Campo para activar/desactivar Contact Form 7 Styler
        add_settings_field(
            'cf7_enabled',
            __('Contact Form 7 Styler', 'bosque-elementor-extras'),
            array($this, 'render_checkbox_field'),
            'bosque-elementor-extras',
            'bosque_elementor_extras_features_section',
            array(
                'id' => 'cf7_enabled',
                'label' => __('Habilitar widget de Contact Form 7 Styler', 'bosque-elementor-extras'),
                'description' => __('Añade un widget para personalizar formularios de Contact Form 7 en Elementor.', 'bosque-elementor-extras')
            )
        );

        // Aquí se pueden añadir más campos para futuras funcionalidades
    }

    /**
     * Sanitizar opciones
     * 
     * @param array $input Opciones a sanitizar
     * @return array
     */
    public function sanitize_options($input) {
        $sanitized_input = array();

        // Sanitizar breadcrumb_enabled
        $sanitized_input['breadcrumb_enabled'] = isset($input['breadcrumb_enabled']) ? 'yes' : 'no';
        
        // Sanitizar cf7_enabled
        $sanitized_input['cf7_enabled'] = isset($input['cf7_enabled']) ? 'yes' : 'no';

        // Aquí se pueden sanitizar más opciones para futuras funcionalidades

        return $sanitized_input;
    }

    /**
     * Renderizar sección de funcionalidades
     */
    public function render_features_section() {
        echo '<p>' . __('Activa o desactiva las funcionalidades del plugin.', 'bosque-elementor-extras') . '</p>';
    }

    /**
     * Renderizar campo de checkbox
     * 
     * @param array $args Argumentos del campo
     */
    public function render_checkbox_field($args) {
        $id = $args['id'];
        $label = $args['label'];
        $description = isset($args['description']) ? $args['description'] : '';
        $checked = $this->get_option($id) === 'yes' ? 'checked' : '';

        echo '<label for="' . esc_attr($id) . '">';
        echo '<input type="checkbox" id="' . esc_attr($id) . '" name="bosque_elementor_extras_options[' . esc_attr($id) . ']" value="yes" ' . $checked . '>';
        echo esc_html($label);
        echo '</label>';

        if (!empty($description)) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }

    /**
     * Renderizar página de ajustes
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Comprobar si se ha enviado el formulario
        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'bosque_elementor_extras_messages',
                'bosque_elementor_extras_message',
                __('Ajustes guardados.', 'bosque-elementor-extras'),
                'updated'
            );
        }

        // Mostrar mensajes de error/éxito
        settings_errors('bosque_elementor_extras_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('bosque_elementor_extras_options_group');
                do_settings_sections('bosque-elementor-extras');
                submit_button(__('Guardar ajustes', 'bosque-elementor-extras'));
                ?>
            </form>
        </div>
        <?php
    }
}

// Inicializar la clase
Bosque_Extras_Admin_Settings::get_instance();
