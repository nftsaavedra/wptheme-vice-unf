<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) exit;

$reglamentos_data = isset( $args['reglamentos_data'] ) ? $args['reglamentos_data'] : array();

if ( empty( $reglamentos_data['data'] ) ) {
    echo '<p>' . esc_html__( 'No se encontraron reglamentos en las categorías especificadas.', 'viceunf' ) . '</p>';
    return;
}

if ( $reglamentos_data['is_grouped'] ) {
    // Render grouped layout
    echo '<div class="reglamentos-container grouped">';
    foreach ( $reglamentos_data['data'] as $grupo ) {
        echo '<div class="reglamento-category-group" style="--category-color: ' . esc_attr( $grupo['color'] ) . ';">';
        echo '<h3 class="category-title">' . esc_html( $grupo['term_name'] ) . '</h3>';
        echo '<ul class="reglamentos-list">';
        foreach ( $grupo['reglamentos'] as $reg ) {
            ?>
            <li>
                <a href="<?php echo esc_url( $reg['permalink'] ); ?>" class="reglamento-main-link">
                    <i class="fas fa-file-alt"></i>
                    <span><?php echo esc_html( $reg['title'] ); ?></span>
                </a>
                <?php if ( $reg['has_file'] ) : ?>
                    <a href="<?php echo esc_url( $reg['file_url'] ); ?>" target="_blank" class="button-download-shortcode"><?php esc_html_e( 'Descargar', 'viceunf' ); ?></a>
                <?php endif; ?>
            </li>
            <?php
        }
        echo '</ul>';
        echo '</div>';
    }
    echo '</div>';
} else {
    // Render flat layout
    echo '<div class="reglamentos-container">';
    echo '<ul class="reglamentos-list">';
    foreach ( $reglamentos_data['data'] as $reg ) {
        ?>
        <li>
            <a href="<?php echo esc_url( $reg['permalink'] ); ?>" class="reglamento-main-link">
                <i class="fas fa-file-alt"></i>
                <span><?php echo esc_html( $reg['title'] ); ?></span>
            </a>
            <?php if ( $reg['has_file'] ) : ?>
                <a href="<?php echo esc_url( $reg['file_url'] ); ?>" target="_blank" class="button-download-shortcode"><?php esc_html_e( 'Descargar', 'viceunf' ); ?></a>
            <?php endif; ?>
        </li>
        <?php
    }
    echo '</ul>';
    echo '</div>';
}
