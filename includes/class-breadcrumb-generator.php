<?php
/**
 * Clase para generar breadcrumbs
 * 
 * @package Bosque\Elementor\Breadcrumb
 */

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase para generar breadcrumbs
 */
class Breadcrumb_Generator {

    /**
     * Opciones de configuración
     * @var array
     */
    private $options;

    /**
     * Constructor
     * 
     * @param array $options Opciones de configuración
     */
    public function __construct($options = []) {
        $this->options = wp_parse_args($options, [
            'home_title'         => __('Inicio', 'bosque-elementor-breadcrumb'),
            'separator'          => '›',
            'show_home'          => true,
            'show_current'       => true,
            'show_on_home'       => false,
            'container_class'    => 'bosque-breadcrumb',
            'item_class'         => 'bosque-breadcrumb-item',
            'link_class'         => 'bosque-breadcrumb-link',
            'current_class'      => 'bosque-breadcrumb-current',
            'separator_class'    => 'bosque-breadcrumb-separator',
        ]);
    }

    /**
     * Generar breadcrumb
     * 
     * @return string HTML del breadcrumb
     */
    public function generate() {
        global $post, $wp_query;
        
        $output = '';
        $home_url = home_url('/');
        $home_link = $this->options['home_title'];
        
        // Clases CSS
        $container_class = esc_attr($this->options['container_class']);
        $item_class = esc_attr($this->options['item_class']);
        $link_class = esc_attr($this->options['link_class']);
        $current_class = esc_attr($this->options['current_class']);
        $separator_class = esc_attr($this->options['separator_class']);
        
        // Separador
        $separator = '<span class="' . $separator_class . '">' . esc_html($this->options['separator']) . '</span>';
        
        // Iniciar contenedor
        $output .= '<nav aria-label="' . esc_attr__('Breadcrumb', 'bosque-elementor-breadcrumb') . '">';
        $output .= '<ol class="' . $container_class . '">';
        
        // Página de inicio
        if ($this->options['show_home']) {
            $output .= '<li class="' . $item_class . '">';
            $output .= '<a class="' . $link_class . '" href="' . esc_url($home_url) . '">' . esc_html($home_link) . '</a>';
            
            // Si no estamos en la página de inicio, agregar separador
            if (!is_front_page()) {
                $output .= $separator;
            }
            
            $output .= '</li>';
        }
        
        // Página de inicio
        if (is_home() || is_front_page()) {
            if (is_front_page() && $this->options['show_on_home']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html(get_bloginfo('name')) . '</li>';
            } elseif (is_home() && $this->options['show_current']) {
                $page_for_posts_id = get_option('page_for_posts');
                if ($page_for_posts_id) {
                    $post_title = get_the_title($page_for_posts_id);
                    $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html($post_title) . '</li>';
                }
            }
        } elseif (is_category()) {
            // Categoría
            $cat = get_category(get_query_var('cat'), false);
            
            if ($cat->parent != 0) {
                $parent_categories = $this->get_category_parents($cat->parent);
                foreach ($parent_categories as $parent) {
                    $output .= '<li class="' . $item_class . '">' . $parent . $separator . '</li>';
                }
            }
            
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html(single_cat_title('', false)) . '</li>';
            }
        } elseif (is_search()) {
            // Búsqueda
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html__('Resultados de búsqueda para', 'bosque-elementor-breadcrumb') . ' "' . get_search_query() . '"</li>';
            }
        } elseif (is_day()) {
            // Archivo diario
            $output .= '<li class="' . $item_class . '"><a class="' . $link_class . '" href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a>' . $separator . '</li>';
            $output .= '<li class="' . $item_class . '"><a class="' . $link_class . '" href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . esc_html(get_the_time('F')) . '</a>' . $separator . '</li>';
            
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html(get_the_time('d')) . '</li>';
            }
        } elseif (is_month()) {
            // Archivo mensual
            $output .= '<li class="' . $item_class . '"><a class="' . $link_class . '" href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a>' . $separator . '</li>';
            
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html(get_the_time('F')) . '</li>';
            }
        } elseif (is_year()) {
            // Archivo anual
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html(get_the_time('Y')) . '</li>';
            }
        } elseif (is_single() && !is_attachment()) {
            // Entrada individual
            if (get_post_type() != 'post') {
                // Custom post type
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                $slug_url = '';
                
                // Verificar si existe el índice 'slug' en el array $slug
                if (is_array($slug) && isset($slug['slug'])) {
                    $slug_url = $slug['slug'];
                } elseif (is_object($slug) && isset($slug->slug)) {
                    $slug_url = $slug->slug;
                } elseif (is_string($slug)) {
                    $slug_url = $slug;
                }
                
                $output .= '<li class="' . $item_class . '"><a class="' . $link_class . '" href="' . esc_url($home_url . $slug_url . '/') . '">' . esc_html($post_type->labels->singular_name) . '</a>' . $separator . '</li>';
                
                if ($this->options['show_current']) {
                    $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html(get_the_title()) . '</li>';
                }
            } else {
                // Post
                $cat = get_the_category();
                if (!empty($cat)) {
                    $cat = $cat[0];
                    $parent_categories = $this->get_category_parents($cat->term_id);
                    foreach ($parent_categories as $parent) {
                        $output .= '<li class="' . $item_class . '">' . $parent . $separator . '</li>';
                    }
                }
                
                if ($this->options['show_current']) {
                    $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html(get_the_title()) . '</li>';
                }
            }
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404()) {
            // Archivo de post type
            $post_type = get_post_type_object(get_post_type());
            
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html($post_type->labels->singular_name) . '</li>';
            }
        } elseif (is_attachment()) {
            // Adjunto
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            
            if (!empty($cat)) {
                $cat = $cat[0];
                $parent_categories = $this->get_category_parents($cat->term_id);
                foreach ($parent_categories as $parent) {
                    $output .= '<li class="' . $item_class . '">' . $parent . $separator . '</li>';
                }
            }
            
            $output .= '<li class="' . $item_class . '"><a class="' . $link_class . '" href="' . esc_url(get_permalink($parent)) . '">' . esc_html($parent->post_title) . '</a>' . $separator . '</li>';
            
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html(get_the_title()) . '</li>';
            }
        } elseif (is_page() && !$post->post_parent) {
            // Página sin padre
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html(get_the_title()) . '</li>';
            }
        } elseif (is_page() && $post->post_parent) {
            // Página con padre
            $parent_id = $post->post_parent;
            $breadcrumbs = [];
            
            while ($parent_id) {
                $page = get_post($parent_id);
                $breadcrumbs[] = '<a class="' . $link_class . '" href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html(get_the_title($page->ID)) . '</a>';
                $parent_id = $page->post_parent;
            }
            
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb) {
                $output .= '<li class="' . $item_class . '">' . $crumb . $separator . '</li>';
            }
            
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html(get_the_title()) . '</li>';
            }
        } elseif (is_tag()) {
            // Etiqueta
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html(single_tag_title('', false)) . '</li>';
            }
        } elseif (is_author()) {
            // Autor
            global $author;
            $userdata = get_userdata($author);
            
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html($userdata->display_name) . '</li>';
            }
        } elseif (is_404()) {
            // 404
            if ($this->options['show_current']) {
                $output .= '<li class="' . $item_class . ' ' . $current_class . '">' . esc_html__('Error 404', 'bosque-elementor-breadcrumb') . '</li>';
            }
        }
        
        // Cerrar contenedor
        $output .= '</ol>';
        $output .= '</nav>';
        
        return $output;
    }
    
    /**
     * Obtener padres de categoría
     * 
     * @param int $id ID de la categoría
     * @return array Arreglo con los padres de la categoría
     */
    private function get_category_parents($id) {
        $chain = [];
        $link_class = esc_attr($this->options['link_class']);
        
        $parent = get_term($id, 'category');
        if (is_wp_error($parent)) {
            return $chain;
        }
        
        $name = $parent->name;
        $chain[] = '<a class="' . $link_class . '" href="' . esc_url(get_category_link($parent->term_id)) . '">' . esc_html($name) . '</a>';
        
        if ($parent->parent && ($parent->parent != $parent->term_id)) {
            $parent_links = $this->get_category_parents($parent->parent);
            $chain = array_merge($parent_links, $chain);
        }
        
        return $chain;
    }
}
