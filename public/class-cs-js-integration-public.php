<?php

namespace amb_dev\CS_JSI;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cs_Js_Integration
 * @subpackage Cs_Js_Integration/public
 * @author     Alwyn Barry <alwyn_barry@yahoo.co.uk>
 * @link       https://https://github.com/AlwynBarry
 * @since      1.0.0
 */
class Cs_Js_Integration_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	/**
	 * The Class Names and file names to be included
	 *
	 * @since    1.0.0
	 * @access   private
	 * @const    array of string    $CS_CLASS_NAMES    The class names of the dependencies needed.
	 */
	private const CS_CLASS_NAMES = array(
			'Cs_Js_Shortcode' => 'class-cs-js-shortcode.php',
			'Cs_Js_Event_Cards_Shortcode' => 'class-cs-js-event-cards-shortcode.php',
			'Cs_Js_Event_List_Shortcode' => 'class-cs-js-event-list-shortcode.php',
			'Cs_Js_Calendar_Shortcode' => 'class-cs-js-calendar-shortcode.php',
			'Cs_Js_Smallgroups_Shortcode' => 'class-cs-js-smallgroups-shortcode.php',
			'Cs_Js_Calendar_Shortcode' => 'class-cs-js-calendar-shortcode.php',
		);

	/**
	 * The shortcode names and their corresponding static functions that
	 * will be called to execute the shortcodes.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @const    array of string => string   The shortcode names and their corresponding function names.
	 */
	private const SHORTCODE_FUNCTION_NAMES = array(
			'cs-js-event-cards' => 'cs_js_event_cards_shortcode',
			'cs-js-event-list' => 'cs_js_event_list_shortcode',
			'cs-js-calendar' => 'cs_js_calendar_shortcode',
			'cs-js-smallgroups' => 'cs_js_smallgroups_shortcode',
			'cs-js-calendar' => 'cs_js_calendar_shortcode',
		);

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->load_dependencies();

	}

	/**
	 * Load the required dependencies for the public side of this plugin.
	 * - the classes which give the behaviour of the shortcodes,
	 * - the static functions that can be called to execute the shortcodes.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * Dependencies: the classes that form the shortcode
		 */
		foreach ( Cs_Js_Integration_Public::CS_CLASS_NAMES as $class_name => $file_name) {
			require_once plugin_dir_path( dirname(__FILE__) ) . 'public/shortcodes/' . $file_name;
		}

	}

		
	/**
	 * Register all of the shortcodes for the public-facing functionality of the plugin
	 * Called as part of the start of the plugin execution, as we set up the plugin 
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function register_shortcodes() {
		
		foreach ( Cs_Js_Integration_Public::SHORTCODE_FUNCTION_NAMES as $shortCodeName => $functionName ) {
			add_shortcode( $shortCodeName, __NAMESPACE__ . "\\" . $functionName );
		}
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cs-js-integration-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_scripts() {

		/*
		 * We are using plain JavaScript.  This is the original boilerplate code for jQuery.
		 * wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/csjs-integration-public.js', array( 'jquery' ), $this->version, false );
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cs-js-integration-public.js', array(), $this->version, false );
		/*
		 * Load the Alpine.js framework. We would love to use their CDN - see https://alpinejs.dev/start-here
		 * wp_enqueue_script( 'alpine', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', array(), $this->version, array( 'strategy'  => 'defer', 'infooter' => 'true', ) );
		 * but WordPress don't allow that - we have to load the file from a local directory and maintain it there.
		 */
 		wp_enqueue_script( 'alpine', plugin_dir_url( __FILE__ ) . 'js/alpinejs-3-x-x-min.js', array(), $this->version, array( 'strategy'  => 'defer', 'infooter' => 'true', ) );
		/*
		 * Load the ChurchSuite framework. We would love to use their CDN - see https://github.com/ChurchSuite/embed-json-script
		 * wp_enqueue_script( 'churchsuite', 'https://cdn.jsdelivr.net/npm/@churchsuite/embed@^5.2.3/dist/cdn.min.js', array(), $this->version, array( 'infooter' => 'true', ) );
		 * but WordPress don't allow that - we have to load the file from a local directory and maintain it there.
		 */
		wp_enqueue_script( 'churchsuite', plugin_dir_url( __FILE__ ) . 'js/churchsuite-embed-5.2.3.js', array(), $this->version, array( 'infooter' => 'true', ) );

	}

}
