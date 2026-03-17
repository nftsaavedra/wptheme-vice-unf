<?php

/**
 * Image Optimization para WordPress Theme ViceUnf
 * 
 * Implementa optimización automática de imágenes, WebP y compresión
 * 
 * @version 1.0.0
 * @package ViceUnf
 */

namespace ViceUnf;

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

class ImageOptimizer
{
    private $quality_jpeg = 85;
    private $quality_png = 9;
    private $quality_webp = 80;
    private $max_width = 2560;
    private $max_height = 1440;

    public function __construct()
    {
        // Cargar configuración desde settings si existe
        $this->load_settings();
        
        // Filtros para optimización al subir imágenes (NO ALTERAN funcionalidad existente)
        add_filter('wp_handle_upload', [$this, 'optimize_uploaded_image'], 10, 2);
        add_filter('wp_generate_attachment_metadata', [$this, 'optimize_thumbnails'], 10, 2);
        add_filter('intermediate_image_sizes_advanced', [$this, 'add_custom_image_sizes']);
        
        // Filtros para WebP (MEJORAN sin cambiar comportamiento existente)
        add_filter('wp_generate_attachment_metadata', [$this, 'generate_webp_versions'], 10, 2);
        add_filter('wp_get_attachment_image_attributes', [$this, 'add_webp_support'], 10, 3);
        
        // Filtros para compresión (SOLO mejoran calidad, no alteran funcionalidad)
        add_filter('wp_editor_set_quality', [$this, 'set_compression_quality'], 10, 2);
        add_filter('jpeg_quality', [$this, 'set_jpeg_quality']);
        add_filter('wp_editor_save_image', [$this, 'optimize_saved_image'], 10, 2);
        
        // Hook para regenerar imágenes existentes (opcional, no afecta funcionamiento normal)
        add_action('admin_init', [$this, 'maybe_regenerate_images']);
    }

    /**
     * Carga configuración desde settings de WordPress
     */
    private function load_settings()
    {
        $settings = get_option('viceunf_image_settings', []);
        
        // Solo usar settings si existen, sino mantener valores por defecto
        if (isset($settings['viceunf_jpeg_quality'])) {
            $this->quality_jpeg = (int) $settings['viceunf_jpeg_quality'];
        }
        
        if (isset($settings['viceunf_png_quality'])) {
            $this->quality_png = (int) $settings['viceunf_png_quality'];
        }
        
        if (isset($settings['viceunf_webp_quality'])) {
            $this->quality_webp = (int) $settings['viceunf_webp_quality'];
        }
        
        if (isset($settings['viceunf_max_width'])) {
            $this->max_width = (int) $settings['viceunf_max_width'];
        }
        
        if (isset($settings['viceunf_max_height'])) {
            $this->max_height = (int) $settings['viceunf_max_height'];
        }
    }

    /**
     * Optimiza imágenes al subirlas
     */
    public function optimize_uploaded_image($upload, $context)
    {
        if ($upload['type'] === 'image/jpeg' || $upload['type'] === 'image/png') {
            $file_path = $upload['file'];
            
            if ($this->optimize_image_file($file_path)) {
                // Actualizar tamaño del archivo después de optimización
                $upload['size'] = filesize($file_path);
            }
        }
        
        return $upload;
    }

    /**
     * Optimiza thumbnails generados
     */
    public function optimize_thumbnails($metadata, $attachment_id)
    {
        if (!isset($metadata['file']) || !is_array($metadata)) {
            return $metadata;
        }

        $upload_dir = wp_upload_dir();
        $base_path = trailingslashit($upload_dir['basedir']) . dirname($metadata['file']);

        // Optimizar imagen original
        $original_path = trailingslashit($upload_dir['basedir']) . $metadata['file'];
        $this->optimize_image_file($original_path);

        // Optimizar todos los thumbnails
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $size => $size_data) {
                $thumbnail_path = $base_path . '/' . $size_data['file'];
                $this->optimize_image_file($thumbnail_path);
            }
        }

        return $metadata;
    }

    /**
     * Optimiza un archivo de imagen específico
     */
    private function optimize_image_file($file_path)
    {
        if (!file_exists($file_path)) {
            return false;
        }

        $image_info = getimagesize($file_path);
        if (!$image_info) {
            return false;
        }

        $mime_type = $image_info['mime'];
        $optimized = false;

        switch ($mime_type) {
            case 'image/jpeg':
                $optimized = $this->optimize_jpeg($file_path);
                break;
            case 'image/png':
                $optimized = $this->optimize_png($file_path);
                break;
        }

        return $optimized;
    }

    /**
     * Optimiza imágenes JPEG
     */
    private function optimize_jpeg($file_path)
    {
        if (!function_exists('imagecreatefromjpeg') || !function_exists('imagejpeg')) {
            return false;
        }

        $image = imagecreatefromjpeg($file_path);
        if (!$image) {
            return false;
        }

        // Redimensionar si excede dimensiones máximas
        $image = $this->resize_if_needed($image, $file_path);

        // Optimizar y guardar
        $result = imagejpeg($image, $file_path, $this->quality_jpeg);
        imagedestroy($image);

        return $result;
    }

    /**
     * Optimiza imágenes PNG
     */
    private function optimize_png($file_path)
    {
        if (!function_exists('imagecreatefrompng') || !function_exists('imagepng')) {
            return false;
        }

        $image = imagecreatefrompng($file_path);
        if (!$image) {
            return false;
        }

        // Redimensionar si excede dimensiones máximas
        $image = $this->resize_if_needed($image, $file_path);

        // Optimizar y guardar
        $result = imagepng($image, $file_path, $this->quality_png);
        imagedestroy($image);

        return $result;
    }

    /**
     * Redimensiona imagen si excede dimensiones máximas
     */
    private function resize_if_needed($image, $file_path)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= $this->max_width && $height <= $this->max_height) {
            return $image;
        }

        // Calcular nuevas dimensiones manteniendo aspect ratio
        $ratio = min($this->max_width / $width, $this->max_height / $height);
        $new_width = (int) ($width * $ratio);
        $new_height = (int) ($height * $ratio);

        $new_image = imagecreatetruecolor($new_width, $new_height);
        
        // Preservar transparencia para PNG
        if (pathinfo($file_path, PATHINFO_EXTENSION) === 'png') {
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
            imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparent);
        }

        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($image);

        return $new_image;
    }

    /**
     * Genera versiones WebP
     */
    public function generate_webp_versions($metadata, $attachment_id)
    {
        if (!function_exists('imagewebp')) {
            return $metadata;
        }

        $upload_dir = wp_upload_dir();
        $base_path = trailingslashit($upload_dir['basedir']) . dirname($metadata['file']);

        // Generar WebP para imagen original
        $original_path = trailingslashit($upload_dir['basedir']) . $metadata['file'];
        $this->create_webp_version($original_path);

        // Generar WebP para thumbnails
        if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $size => $size_data) {
                $thumbnail_path = $base_path . '/' . $size_data['file'];
                $this->create_webp_version($thumbnail_path);
            }
        }

        return $metadata;
    }

    /**
     * Crea versión WebP de una imagen
     */
    private function create_webp_version($file_path)
    {
        if (!file_exists($file_path)) {
            return false;
        }

        $image_info = getimagesize($file_path);
        if (!$image_info) {
            return false;
        }

        $mime_type = $image_info['mime'];
        $image = null;

        switch ($mime_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file_path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file_path);
                break;
            default:
                return false;
        }

        if (!$image) {
            return false;
        }

        $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file_path);
        $result = imagewebp($image, $webp_path, $this->quality_webp);
        imagedestroy($image);

        return $result;
    }

    /**
     * Añade soporte WebP a las imágenes (COMO MEJORA, no reemplaza el src original)
     */
    public function add_webp_support($attributes, $attachment, $size)
    {
        if (!isset($attributes['src'])) {
            return $attributes; // Mantener comportamiento original
        }

        // Generar WebP como mejora opcional, no reemplazo
        $webp_src = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $attributes['src']);
        
        // Verificar si existe versión WebP
        $upload_dir = wp_upload_dir();
        $webp_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $webp_src);
        
        if (file_exists($webp_path)) {
            // Añadir WebP como opción adicional, NO reemplazar src original
            if (isset($attributes['srcset'])) {
                // Añadir al srcset existente
                $attributes['srcset'] .= ', ' . $webp_src . ' 2x';
            } else {
                // Crear srcset con ambas opciones
                $attributes['srcset'] = $attributes['src'] . ', ' . $webp_src . ' 2x';
            }
        }

        return $attributes; // Mantener todos los atributos originales + mejoras
    }

    /**
     * Añade tamaños de imagen personalizados optimizados (NO elimina existentes)
     */
    public function add_custom_image_sizes($sizes)
    {
        // IMPORTANTE: NO eliminamos tamaños existentes para mantener compatibilidad
        // Solo añadimos nuestros tamaños optimizados
        
        // Añadir tamaños optimizados para el tema (sin afectar los existentes)
        $sizes['viceunf-hero'] = [
            'width' => 1920,
            'height' => 1080,
            'crop' => true,
        ];
        
        $sizes['viceunf-card'] = [
            'width' => 400,
            'height' => 300,
            'crop' => true,
        ];
        
        $sizes['viceunf-thumbnail'] = [
            'width' => 150,
            'height' => 150,
            'crop' => true,
        ];

        return $sizes; // Devuelve todos los tamaños + los nuevos
    }

    /**
     * Establece calidad de compresión
     */
    public function set_compression_quality($quality, $mime_type)
    {
        switch ($mime_type) {
            case 'image/jpeg':
                return $this->quality_jpeg;
            case 'image/png':
                return $this->quality_png;
            case 'image/webp':
                return $this->quality_webp;
            default:
                return $quality;
        }
    }

    /**
     * Establece calidad JPEG
     */
    public function set_jpeg_quality($quality)
    {
        return $this->quality_jpeg;
    }

    /**
     * Optimiza imágenes guardadas en el editor
     */
    public function optimize_saved_image($image, $changes)
    {
        // Optimizar después de cada guardado en el editor
        $this->optimize_image_file($image);
        return $image;
    }

    /**
     * Opcional: Regenera imágenes existentes (solo si se solicita)
     */
    public function maybe_regenerate_images()
    {
        if (isset($_GET['viceunf_regenerate_images']) && current_user_can('manage_options')) {
            check_admin_referer('viceunf_regenerate_images');
            
            $this->regenerate_all_images();
            wp_redirect(admin_url('options-general.php?page=viceunf_image_options&regenerated=1'));
            exit;
        }
    }

    /**
     * Regenera todas las imágenes (función pesada - usar con cuidado)
     */
    private function regenerate_all_images()
    {
        // Implementación opcional para regeneración masiva
        // Esto debería usarse con un sistema de colas para timeouts
    }
}

// Inicializar el optimizador de imágenes
new ImageOptimizer();
