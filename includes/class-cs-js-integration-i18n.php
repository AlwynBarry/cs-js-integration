<?php

namespace amb_dev\CS_JSI;


/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://githb.com/AlwynBarry/
 * @since      1.0.0
 *
 * @package    Cs_Js_Integration
 * @subpackage Cs_Js_Integration/includes
 * @author     Alwyn Barry <alwyn_barry@yahoo.co.uk>
 */
class Cs_Js_Integration_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		/*
		 * Apparently since WP 6.7 this is no longer needed, according to the Wordpress Directory submission comments
		 *
		 * load_plugin_textdomain( 'cs-js-integration', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
		 */

	}


}
