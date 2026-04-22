<?php

/**
 * Render del bloque vpinunf/icon-categories.
 *
 * @param array    $attributes Atributos.
 * @param string   $content    N/A.
 * @param WP_Block $block      Instancia del bloque.
 */

if (! defined('ABSPATH')) {
    exit;
}

$items        = $attributes['items'] ?? [];
$icon_bg      = $attributes['iconBgColor'] ?? '#ff4700';
$layout       = $attributes['layout'] ?? 'row';
$columns      = isset($attributes['columns']) ? (int) $attributes['columns'] : 6;

$layout_class = 'row' === $layout ? 'vpinunf-icon-categories--row' : 'vpinunf-icon-categories--grid';

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => "vpinunf-icon-categories $layout_class")
);

$css_vars = sprintf(
    '--vpinunf-icon-bg: %s; --vpinunf-icon-cols: %d;',
    esc_attr($icon_bg),
    $columns
);
?>
<div <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($css_vars); ?>">
    <?php foreach ($items as $item) :
        $icon_class = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $item['icon'] ?? 'fa-solid fa-circle');
        $label      = $item['label'] ?? '';
        $url        = $item['url'] ?? '';
        $tag        = $url ? 'a' : 'div';
        $href_attr  = $url ? ' href="' . esc_url($url) . '"' : '';
    ?>
        <<?php echo $tag; ?> class="vpinunf-icon-categories__item" <?php echo $href_attr; ?>>
            <div
                class="vpinunf-icon-categories__circle"
                style="background-color: <?php echo esc_attr($icon_bg); ?>;">
                <i class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></i>
            </div>
            <?php if ($label) : ?>
                <span class="vpinunf-icon-categories__label">
                    <?php echo wp_kses_post($label); ?>
                </span>
            <?php endif; ?>
        </<?php echo $tag; ?>>
    <?php endforeach; ?>
</div>