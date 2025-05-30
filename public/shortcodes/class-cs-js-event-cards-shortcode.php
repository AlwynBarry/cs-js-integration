<?php

namespace amb_dev\CS_JSI;


require_once plugin_dir_path( __FILE__ ) . 'class-cs-js-shortcode.php';
use amb_dev\CS_JSI\Cs_Js_Shortcode as Cs_Js_Shortcode;


/**
 * A child of Cs_Js_Shortcode to provide the creation of the HTML response for small lists
 * of events (likely less than 12, or potentially only 3 in a single line) as 'cards'
 * with the event image and the event details (without description) provided.
 * 
 * This class uses Alpine.js to provide the HTML to display the events as cards.
 * 
 * Below the class we also provide a function which can be supplied to Wordpress to
 * run the ShortCode.  This function creates an instance of the Shortcode class and calls
 * the run_shortcode() function in the class to run the shortcode with the atts supplied.
 * 
 * To call the shortcode, you must supply the church name used in the normal ChurchSuite
 * web url (e.g. from https://mychurch.churchsuite.com/ - 'mychurch' is the name to supply)
 * Use the church_name="mychurch" parameter to supply the church name.  You must also
 * supply the Configuration Hex String which identifies the JSON request to use.
 *
 * @link       https://https://github.com/AlwynBarry
 * @since      1.0.0
 *
 * @package    Cs_Js_Integration
 * @subpackage Cs_Js_Integration/public/shortcodes
 * @author     Alwyn Barry <alwyn_barry@yahoo.co.uk>
 */
class Cs_Js_Event_Cards_Shortcode extends Cs_Js_Shortcode {


    public function __construct( $atts ) {
        parent::__construct( $atts, 'eventCardsAlpine.html' );
	}


	/**
	 * Use the JSON response to create the HTML containing the list of events.
	 * 
	 * 
	 * 
	 * @since	1.0.0
	 * @return	string	the HTML to render the events in cards
	 */
	protected function get_HTML_response() : string {
		
		// Firstly get the JSON using the Configuration passed to the constructor
		$output = "<div x-data=\"CSEvents({configuration: '$this->configuration'})\">";
		// Now output the Alpine code to render the Small Groups
		$output .= $this->alpineHTML;
		// Close the surrounding DIV
		$output .= '</div>' . "\n";

		return $output;
		
	}

}


/**
 * Shortcode to be used in the content.
 *
 * @since 1.0.0
 * @param	array()	$atts	Array supplied by Wordpress of params to the shortcode
 * 							church_name="mychurch" is required - with "mychurch" replaced with your church name
 *                          configuration="a01cef-5ebcf3-c20fe" is required - replace hex string with the configuration hex string
 */
function cs_js_event_cards_shortcode( $atts ) {
	return ( new Cs_Js_Event_Cards_Shortcode( $atts ) )->run_shortcode();
}
