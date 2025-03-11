<?php

namespace amb_dev\CS_JSI;

/**
 * The base class of the shortcodes created for this plugin.
 * This class holds the base ChurchSuite URL and the base ChurchSuite
 * Configuration which the shortcode will use to get the appropriate
 * JSON response from ChurchSuite.
 * 
 * The constructor will take the attributes supplied to the shortcode, check
 * and sanitize them, and then store the ChurchSuite URL and Configuration
 * required to make the JSON API request.  The JSON API request is not made,
 * however, until the run_shortcode() function is called on the object. Behind
 * the scenes, the ChurchSuite library used caches the JSON so the response is
 * usually rapid, though far more items are often returned behind the
 * scenes by the ChurchSuite supplied library than are required.
 *
 * To create a shortcode of your own, extend this class and provide a
 * get_HTML_response() function.  The supplied get_HTML_response() function
 * should output the container HTML and use Alpine.js to iterate over the
 * objects returned from the ChurchSuite to output each object.
 * run_shortcode() will ensure the code to fetch the JSON is inserted into
 * the response and then dispatch to get_response().
 *
 * @link       https://https://github.com/AlwynBarry
 * @since      1.0.0
 *
 * @package    Cs_Js_Integration
 * @subpackage Cs_Js_Integration/public/shortcodes
 * @author     Alwyn Barry <alwyn_barry@yahoo.co.uk>
 */
abstract class Cs_Js_Shortcode {

	/*
	 * The root url for churchsuite, minus the church name.
	 * Provided as a const so it can be easily changed if ChurchSuite changes in the future.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @const    string 	The root url for churchsuite, minus the churchname
	 */
	private const CHURCHSUITE_URL = '.churchsuite.com';

    protected const DEFAULT_ATTS = array( 'church_name' => '', 'configuration' => '' );

    protected string $church_name;
    protected string $configuration;

	public function __construct( $atts ) {
	    // set defaults
		$sc_atts = shortcode_atts( \amb_dev\CS_JSI\Cs_Js_Shortcode::DEFAULT_ATTS, $atts );

		// Get the church name parameter
		$this->church_name = $sc_atts[ 'church_name' ] ?? '';
		// Sanitize the church_name so it is simply a-zA-Z
		$this->church_name = strtolower( preg_replace( '/[^a-zA-Z]+/', '', $this->church_name ) );

		// Get the configuration parameter
		$this->configuration = $sc_atts[ 'configuration' ] ?? '';
		// Sanitize the configuration so it is a string of hex separated by hyphens
		$this->configuration = rtrim( trim( preg_replace( '/-+/', '-', preg_replace( '/[^a-f0-9-]+/', "", strtolower( $this->configuration ) ) ), '-'), '-');
	}
	
	protected function get_church_url_script() {
	    return '<script>CS.url = "https://' . $this->church_name . self::CHURCHSUITE_URL . '";</script>';
	}

	/*
	 * This is the function the child class must implement that will return the HTML
	 * response from the JSON ChurchSuite response.
	 * 
	 * It should output any container HTML required, and then iterate over the objects
	 * using Alpine.js to generate the required HTML.
	 * 
 	 * @since	1.0.0
 	 * @return	string 	The string with the HTML of the shortcode response
	 */
	protected abstract function get_HTML_response() : string;

	/*
	 * Run the shortcode to produce the HTML output
	 * 
	 * Add the inline script which causes the ChurchSuite library to get the JSON
	 * response from cache if available, or from ChurchSuite.  Then call
	 * $this->get_HTML_response() which must use Alpine.js to iterate the returned
	 * objects and return the relevant HTML.
	 * 
  	 * @since	1.0.0
	 * @return	string	a string with the HTML of the ChurchSuite JSON response
	 */
	 public function run_shortcode() : string {
		return $this->get_church_url_script() . $this->get_HTML_response();
	}

}
