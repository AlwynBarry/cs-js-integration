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

	/**
	 * The root url for churchsuite, minus the church name.
	 * Provided as a const so it can be easily changed if ChurchSuite changes in the future.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @const    string 	The root url for churchsuite, minus the churchname
	 */
	private const CHURCHSUITE_URL = '.churchsuite.com';

	/**
	 * The default shortcode attributes.  Only two attributes are expected because
	 * all of the parameters for the churchsuite event or group details required
	 * are held in the embed configuration provided by the caller.
	 * Provided as a const so it can be easily changed if ChurchSuite changes in the future.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @const    string 	DEFAULT_ATTS    The default [no source] shortcode attributes
	 */
    protected const DEFAULT_ATTS = array( 'church_name' => '', 'configuration' => '' );

	/**
	 * The church name used to construct the JSON url.  This is the name of the church
	 * used when you would call your instance of churchsuite normally - i.e.
	 * if you use 'https://mychurch.churchsuite.com/' then 'mychurch' is the church name.
	 * The value provided is sanitized to contain A-Za-z only.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @const    string 	church_name    The church name from the normal ChurchSuite URL
	 */
    protected readonly string $church_name;

	/**
	 * The embed configuration identifier to be used to obtain the JSON data from
	 * the ChurchSuite API.  This is a hyphen-separated hex string.  The constructor
	 * sanitizes the input to ensure that only hex and hyphens are permitted.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @const    string 	configuration    The unique embed configuration hex identifier
	 */
    protected readonly string $configuration;

	/**
	 * The filename of the alpineHTML file to be used to create the output of this
	 * shortcode.  The Alpine.js HTML is kept in a separate file so that it can be
	 * easily modified or replaced to create a different output arrangement if needed.
	 *
	 * NOTE: we cannot use 'here' docs to create the Alpine.js html in this shortcode
	 *       because 'here' docs are not permitted within Wordpress plugins.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @const    string 	alpineHTML    The filename of the alpine HTML to use to create
	 *                                    the output of this shortcode.
	 */
    protected readonly string $alpineHTML;
 
    
    /**
     * The constructor expects a church_name and a configuration parameter, and will
     * use these to set these properties once the parameters are sanitized.
     * A child class should call this constructor and then set the $alpineHTML
     * (e.g. by reading a file with the HTML, or manually setting it up in-line)
     * 
     * @since 1.0.0
     * @param array	$atts	      An array with the keys:
     * 							      church_name		The name of the church which appears immediately
     * 									        		after https:// when logging in to your ChurchSuite
     * 							      configuration 	The Hex string of the Embed Configuration to use
     * @param string $fileName    The filename of the Alpine HTML to be used by the child class
	 */
	public function __construct( $atts, string $fileName ) {
	    // set defaults
		$sc_atts = shortcode_atts( \amb_dev\CS_JSI\Cs_Js_Shortcode::DEFAULT_ATTS, $atts );

		// Get the church name parameter
		$church_name = $sc_atts[ 'church_name' ] ?? '';
		// Sanitize the church_name so it is simply a-zA-Z
		$this->church_name = strtolower( preg_replace( '/[^a-zA-Z]+/', '', $church_name ) );

		// Get the configuration parameter
		$configuration = $sc_atts[ 'configuration' ] ?? '';
		// Sanitize the configuration so it is a string of hex separated by hyphens
		$this->configuration = rtrim( trim( preg_replace( '/-+/', '-', preg_replace( '/[^a-f0-9-]+/', "", strtolower( $configuration ) ) ), '-'), '-');

		// Read in the Alpine HTML required for the child instance 
        $this->alpineHTML = file_get_contents( plugin_dir_path( __FILE__ ) . '../inc/' . $fileName );

	}

	
	/**
	 * A helper function which returns the HTML that will be the root of the JSON call
	 * This is called for you by the run_shortcode() function before it dispatches to
	 * get_HTML_response() in the child class. 
	 * 
 	 * @since	1.0.0
 	 * @return	string 	A string of HTML which contains a JS script that sets the Churchsuite
 	 * 					URL that the ChurchSuite Library will use to fetch the JSON
	 */
	private function get_church_url_script() {
	    return '<script>CS.url = "https://' . $this->church_name . self::CHURCHSUITE_URL . '";</script>';
	}

	/**
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


	/**
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
