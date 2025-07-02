<?php
/**
 * Singleton class for handling the theme's customizer integration.
 */
final class ViceUnf_Customize {

	/**
	 * Returns the instance.
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ),999 );
	}

	/**
	 * Sets up the customizer sections.
	 */
	public function sections( $manager ) {
        // Si necesitas otras secciones personalizadas, agrégalas aquí.
    }
}

// Eliminar la sección 'softme' del personalizador para ocultar la promoción del tema padre
add_action('customize_register', function($manager) {
    $manager->remove_section('softme');
}, 1000);

// Doing this customizer thang!
ViceUnf_Customize::get_instance();