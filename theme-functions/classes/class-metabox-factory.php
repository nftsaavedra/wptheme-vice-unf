<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Clase genérica para la creación dinámica de Meta Boxes.
 * Implementa el principio DRY al unificar la renderización HTML de los campos
 * y el proceso seguro de guardado para cualquier Custom Post Type.
 */
class ViceUnf_Metabox_Factory {

    private $id;
    private $title;
    private $post_type;
    private $context;
    private $priority;
    private $fields;
    private $save_callback;

    public function __construct( $config ) {
        $this->id            = $config['id'];
        $this->title         = $config['title'];
        $this->post_type     = $config['post_type'];
        $this->context       = isset( $config['context'] ) ? $config['context'] : 'normal';
        $this->priority      = isset( $config['priority'] ) ? $config['priority'] : 'high';
        $this->fields        = $config['fields'];
        $this->save_callback = isset( $config['save_callback'] ) ? $config['save_callback'] : null;

        add_action( 'add_meta_boxes_' . $this->post_type, array( $this, 'add_meta_box' ) );
        add_action( 'save_post_' . $this->post_type, array( $this, 'save_meta_box' ) );
    }

    public function add_meta_box() {
        add_meta_box(
            $this->id,
            $this->title,
            array( $this, 'render_meta_box' ),
            $this->post_type,
            $this->context,
            $this->priority
        );
    }

    public function render_meta_box( $post ) {
        // Nonce único para este metabox
        wp_nonce_field( "{$this->id}_nonce_action", "{$this->id}_nonce_name" );

        // Renderizado del layout
        $has_sections = false;
        foreach ( $this->fields as $field ) {
            if ( isset( $field['section'] ) ) {
                $has_sections = true;
                break;
            }
        }

        if ( $has_sections ) {
            $sections = array();
            foreach ( $this->fields as $field ) {
                $sec = isset( $field['section'] ) ? $field['section'] : 'General';
                $sections[ $sec ][] = $field;
            }

            foreach ( $sections as $section_title => $section_fields ) {
                echo '<div class="metabox-section slider-metabox-section">';
                if ( $section_title !== 'General' ) {
                    echo '<h4>' . esc_html( $section_title ) . '</h4>';
                }
                foreach ( $section_fields as $field ) {
                    $this->render_field( $field, $post );
                }
                echo '</div>';
            }
        } else {
            foreach ( $this->fields as $field ) {
                $this->render_field( $field, $post );
            }
        }
    }

    private function render_field( $field, $post ) {
        $meta_key = $field['meta_key'] ?? '';
        $value = $meta_key ? get_post_meta( $post->ID, $meta_key, true ) : '';
        $id = $field['id'];
        $type = $field['type'] ?? 'text';
        $label = $field['label'] ?? '';
        $attributes = $field['attributes'] ?? '';
        $wrapper_class = $field['wrapper_class'] ?? 'metabox-field-wrapper slider-field';
        $wrapper_id = $field['wrapper_id'] ?? '';
        $wrapper_attr = $wrapper_id ? 'id="' . esc_attr($wrapper_id) . '"' : '';

        // Ocultar si hay style display:none en attributes del wrapper (para conditional fields)
        $wrapper_style = $field['wrapper_style'] ?? '';
        $style_attr = $wrapper_style ? 'style="' . esc_attr($wrapper_style) . '"' : '';

        echo "<div class='" . esc_attr( $wrapper_class ) . "' $wrapper_attr $style_attr>";
        
        if ( $label && $type !== 'custom_html' ) {
            echo "<label for='" . esc_attr( $id ) . "'><strong>" . wp_kses_post( $label ) . "</strong></label><br>";
        }

        switch ( $type ) {
            case 'text':
            case 'url':
            case 'date':
            case 'time':
                echo "<input type='" . esc_attr( $type ) . "' id='" . esc_attr( $id ) . "' name='" . esc_attr( $id ) . "' value='" . esc_attr( $value ) . "' " . $attributes . " class='large-text' />";
                break;

            case 'hidden':
                echo "<input type='hidden' id='" . esc_attr( $id ) . "' name='" . esc_attr( $id ) . "' value='" . esc_attr( $value ) . "' " . $attributes . " />";
                break;

            case 'textarea':
                echo "<textarea id='" . esc_attr( $id ) . "' name='" . esc_attr( $id ) . "' rows='3' class='large-text' {$attributes}>" . esc_textarea( $value ) . "</textarea>";
                break;

            case 'select':
                echo "<select id='" . esc_attr( $id ) . "' name='" . esc_attr( $id ) . "' {$attributes}>";
                foreach ( $field['options'] as $opt_value => $opt_label ) {
                    echo "<option value='" . esc_attr( $opt_value ) . "' " . selected( $value, $opt_value, false ) . ">" . esc_html( $opt_label ) . "</option>";
                }
                echo "</select>";
                break;

            case 'radio':
                echo "<div class='radio-buttons-as-tabs'>";
                if ( empty( $value ) && isset( $field['default'] ) ) {
                    $value = $field['default'];
                }
                foreach ( $field['options'] as $opt_value => $opt_label ) {
                    echo "<input type='radio' id='" . esc_attr( $id . '_' . $opt_value ) . "' name='" . esc_attr( $id ) . "' value='" . esc_attr( $opt_value ) . "' " . checked( $value, $opt_value, false ) . " {$attributes}>";
                    echo "<label for='" . esc_attr( $id . '_' . $opt_value ) . "'>" . esc_html( $opt_label ) . "</label>";
                }
                echo "</div>";
                break;

            case 'custom_html':
                if ( is_callable( $field['render_callback'] ) ) {
                    call_user_func( $field['render_callback'], $post, $field, $value );
                }
                break;
        }

        if ( isset( $field['description'] ) && $type !== 'custom_html' ) {
            echo "<p class='description'>" . wp_kses_post( $field['description'] ) . "</p>";
        }

        echo "</div>";
    }

    public function save_meta_box( $post_id ) {
        // Validaciones DRY
        if ( ! isset( $_POST[ "{$this->id}_nonce_name" ] ) || ! wp_verify_nonce( $_POST[ "{$this->id}_nonce_name" ], "{$this->id}_nonce_action" ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Bucle dinámico por cada campo
        foreach ( $this->fields as $field ) {
            if ( ! isset( $field['meta_key'] ) || empty( $field['meta_key'] ) ) {
                continue; // Saltar si no almacena metadatos automáticos
            }

            $id = $field['id'];
            $meta_key = $field['meta_key'];

            if ( isset( $_POST[ $id ] ) ) {
                $type = $field['type'] ?? 'text';
                $value = $_POST[ $id ];

                // Sanitización dinámica según tipo
                switch ( $type ) {
                    case 'url':
                        $sanitized_value = esc_url_raw( $value );
                        break;
                    case 'textarea':
                        $sanitized_value = sanitize_textarea_field( $value );
                        break;
                    case 'text':
                    case 'date':
                    case 'time':
                    case 'select':
                    case 'radio':
                    case 'hidden':
                    default:
                        $sanitized_value = sanitize_text_field( $value );
                        break;
                }

                update_post_meta( $post_id, $meta_key, $sanitized_value );
            }
        }
        
        // Ejecutar callback de guardado personalizado si existe
        if ( $this->save_callback && is_callable( $this->save_callback ) ) {
             call_user_func( $this->save_callback, $post_id, $_POST );
        }
    }
}
