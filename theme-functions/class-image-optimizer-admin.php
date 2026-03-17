<?php

/**
 * Admin Options para Image Optimizer
 * 
 * Interfaz de administración para configurar optimización de imágenes
 * 
 * @version 1.0.0
 * @package ViceUnf
 */

namespace ViceUnf;

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

class ImageOptimizerAdmin
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Añade menú de administración
     */
    public function add_admin_menu()
    {
        add_options_page(
            'ViceUnf Image Optimizer',
            'Image Optimizer',
            'manage_options',
            'viceunf_image_options',
            [$this, 'admin_page']
        );
    }

    /**
     * Registra configuraciones
     */
    public function register_settings()
    {
        register_setting('viceunf_image_options', 'viceunf_image_settings');

        add_settings_section(
            'viceunf_image_quality',
            'Calidad de Imagen',
            [$this, 'quality_section_callback'],
            'viceunf_image_options'
        );

        add_settings_field(
            'viceunf_jpeg_quality',
            'Calidad JPEG',
            [$this, 'quality_field_callback'],
            'viceunf_image_options',
            'viceunf_image_quality',
            ['type' => 'jpeg']
        );

        add_settings_field(
            'viceunf_png_quality',
            'Calidad PNG',
            [$this, 'quality_field_callback'],
            'viceunf_image_options',
            'viceunf_image_quality',
            ['type' => 'png']
        );

        add_settings_field(
            'viceunf_webp_quality',
            'Calidad WebP',
            [$this, 'quality_field_callback'],
            'viceunf_image_options',
            'viceunf_image_quality',
            ['type' => 'webp']
        );

        add_settings_section(
            'viceunf_image_dimensions',
            'Dimensiones Máximas',
            [$this, 'dimensions_section_callback'],
            'viceunf_image_options'
        );

        add_settings_field(
            'viceunf_max_width',
            'Ancho Máximo (px)',
            [$this, 'dimension_field_callback'],
            'viceunf_image_options',
            'viceunf_image_dimensions',
            ['dimension' => 'width']
        );

        add_settings_field(
            'viceunf_max_height',
            'Alto Máximo (px)',
            [$this, 'dimension_field_callback'],
            'viceunf_image_options',
            'viceunf_image_dimensions',
            ['dimension' => 'height']
        );
    }

    /**
     * Callback de sección de calidad
     */
    public function quality_section_callback()
    {
        echo '<p>Configura la calidad de compresión para diferentes formatos de imagen. Valores más bajos = mayor compresión = menor calidad.</p>';
    }

    /**
     * Callback de campo de calidad
     */
    public function quality_field_callback($args)
    {
        $options = get_option('viceunf_image_settings', []);
        $type = $args['type'];
        $field_name = "viceunf_{$type}_quality";
        $value = isset($options[$field_name]) ? $options[$field_name] : $this->get_default_quality($type);
        
        echo '<input type="range" min="1" max="100" value="' . esc_attr($value) . '" 
                name="viceunf_image_settings[' . $field_name . ']" 
                id="' . esc_attr($field_name) . '" 
                oninput="document.getElementById(\'' . esc_attr($field_name) . '_value\').textContent = this.value">';
        echo '<span id="' . esc_attr($field_name) . '_value">' . esc_html($value) . '</span>%';
        
        $this->show_quality_info($type);
    }

    /**
     * Muestra información sobre calidad
     */
    private function show_quality_info($type)
    {
        $info = [
            'jpeg' => '85% recomendado para balance calidad/tamaño',
            'png' => '9 (compresión) - PNG usa compresión no destructiva',
            'webp' => '80% recomendado para WebP'
        ];
        
        echo '<p class="description">' . esc_html($info[$type]) . '</p>';
    }

    /**
     * Callback de sección de dimensiones
     */
    public function dimensions_section_callback()
    {
        echo '<p>Establece las dimensiones máximas para imágenes subidas. Las imágenes más grandes serán redimensionadas automáticamente.</p>';
    }

    /**
     * Callback de campo de dimensión
     */
    public function dimension_field_callback($args)
    {
        $options = get_option('viceunf_image_settings', []);
        $dimension = $args['dimension'];
        $field_name = "viceunf_max_{$dimension}";
        $value = isset($options[$field_name]) ? $options[$field_name] : $this->get_default_dimension($dimension);
        
        echo '<input type="number" min="800" max="4000" value="' . esc_attr($value) . '" 
                name="viceunf_image_settings[' . $field_name . ']" 
                id="' . esc_attr($field_name) . '" class="small-text"> px';
        
        $this->show_dimension_info($dimension);
    }

    /**
     * Muestra información sobre dimensiones
     */
    private function show_dimension_info($dimension)
    {
        $info = [
            'width' => '2560px recomendado para 4K',
            'height' => '1440px recomendado para 4K'
        ];
        
        echo '<p class="description">' . esc_html($info[$dimension]) . '</p>';
    }

    /**
     * Obtiene calidad por defecto
     */
    private function get_default_quality($type)
    {
        $defaults = [
            'jpeg' => 85,
            'png' => 9,
            'webp' => 80
        ];
        
        return $defaults[$type] ?? 80;
    }

    /**
     * Obtiene dimensión por defecto
     */
    private function get_default_dimension($dimension)
    {
        $defaults = [
            'width' => 2560,
            'height' => 1440
        ];
        
        return $defaults[$dimension] ?? 1920;
    }

    /**
     * Página de administración
     */
    public function admin_page()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php if (isset($_GET['regenerated']) && $_GET['regenerated'] == 1): ?>
                <div class="notice notice-success is-dismissible">
                    <p>Imágenes regeneradas exitosamente.</p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('viceunf_image_options');
                do_settings_sections('viceunf_image_options');
                submit_button();
                ?>
            </form>
            
            <hr>
            
            <h2>Herramientas de Mantenimiento</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Regenerar Imágenes</th>
                    <td>
                        <p>Regenera todos los thumbnails y versiones WebP para imágenes existentes.</p>
                        <p><strong>Advertencia:</strong> Este proceso puede ser intensivo y tomar mucho tiempo.</p>
                        <a href="<?php echo esc_url(wp_nonce_url(admin_url('options-general.php?page=viceunf_image_options&viceunf_regenerate_images=1'), 'viceunf_regenerate_images')); ?>" 
                           class="button" 
                           onclick="return confirm('¿Estás seguro? Este proceso puede tardar varios minutos.')">
                            Regenerar Imágenes
                        </a>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Estadísticas de Optimización</th>
                    <td>
                        <?php $this->show_optimization_stats(); ?>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }

    /**
     * Muestra estadísticas de optimización
     */
    private function show_optimization_stats()
    {
        global $wpdb;
        
        // Contar imágenes optimizadas (estimado)
        $total_images = $wpdb->get_var("
            SELECT COUNT(*) FROM {$wpdb->posts} 
            WHERE post_type = 'attachment' 
            AND post_mime_type LIKE 'image/%'
        ");
        
        $webp_count = 0;
        $upload_dir = wp_upload_dir();
        
        // Contar archivos WebP (estimado)
        if (file_exists($upload_dir['basedir'])) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($upload_dir['basedir'])
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'webp') {
                    $webp_count++;
                }
            }
        }
        
        echo '<div class="viceunf-stats">';
        echo '<p><strong>Total de imágenes:</strong> ' . number_format($total_images) . '</p>';
        echo '<p><strong>Versiones WebP:</strong> ' . number_format($webp_count) . '</p>';
        echo '<p><strong>Tasa de optimización WebP:</strong> ' . 
             ($total_images > 0 ? round(($webp_count / $total_images) * 100, 1) : 0) . '%</p>';
        echo '</div>';
        
        echo '<style>
            .viceunf-stats { 
                background: #f9f9f9; 
                padding: 15px; 
                border-radius: 5px; 
                margin-top: 10px;
            }
            .viceunf-stats p { margin: 5px 0; }
        </style>';
    }
}

// Inicializar administración del optimizador
new ImageOptimizerAdmin();
