<?php
require_once('../../../wp-load.php');

$args = array(
    'post_type'      => 'evento',
    'posts_per_page' => 1,
    'post_status'    => 'any'
);

$query = new WP_Query($args);

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        echo "ID EVENTO: " . get_the_ID() . "\n";
        print_r(get_post_meta(get_the_ID()));
    }
} else {
    echo "No hay eventos.";
}
