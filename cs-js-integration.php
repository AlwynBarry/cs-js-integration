<?php

namespace amb_dev\CS_JSI;


/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://githb.com/AlwynBarry/
 * @since             1.0.0
 * @package           Cs_Js_Integration
 *
 * @wordpress-plugin
 * Plugin Name:       JS Integration for ChurchSuite
 * Plugin URI:        https://github.com/AlwynBarry/cs-js-integration
 * Description:       JS Integration for ChurchSuite provides shortcodes that display JSON data from the public JSON ChurchSuite feeds using the ChurchSuite provided Alpine.js bindings.
 * Version:           1.0.0
 * Author:            Alwyn Barry
 * Author URI:        https://githb.com/AlwynBarry//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cs-js-integration
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CS_JS_INTEGRATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cs-jsintegration-activator.php
 */
function activate_cs_js_integration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cs-js-integration-activator.php';
	\amb_dev\CS_JSI\Cs_Js_Integration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cs-js-integration-deactivator.php
 */
function deactivate_cs_js_integration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cs-js-integration-deactivator.php';
	\amb_dev\CS_JSI\Cs_Js_Integration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate_cs_js_integration' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\deactivate_cs_js_integration' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cs-js-integration.php';
use \amb_dev\CS_JSI\Cs_Js_Integration as Cs_Js_Integration;

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cs_js_integration() {

	$plugin = new Cs_Js_Integration();
	$plugin->run();

}
run_cs_js_integration();
