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
        parent::__construct( $atts );
	}

	/*
	 * Use the JSON response to create the HTML containing the list of events.
	 * 
	 * 
	 * 
	 * @since	1.0.1
	 * @return	string	the HTML to render the events in cards
	 */
	protected function get_HTML_response() : string {

		$output = <<<EOC
  <!-- Tell it which configuration to use... -->
  <div x-data="CSEvents({configuration: '$this->configuration'})">
    <div class="cs-event-cards cs-row">
      <template x-for="event in events">
        <!-- There can only be one element within the template -->
        <div :id="event.identifier" :class="event.status" class="cs-card cs-event-card">
          <div class="cs-event-card-image-area">
            <img :src="event.image?.medium">
          </div>
          <div class="cs-event-card-details-area">
            <div class="cs-event-name">
                <a :href="event.link" class="cs-event-link" target="_blank"><span x-text="event.name"></span></a>
            </div>
            <div class="cs-date">
                <!-- Event times are day.js instances - see https://day.js.org/ for formatting options -->
                <span x-text="event.start.format('ll')" class="cs-date-gliph"></span>
            </div>
            <div class="cs-time">
                <span x-text="event.allDay ? 'All Day' : event.start.format('h:mma')" class="cs-time-gliph cs-start-time"></span>
                <span x-text="event.allDay ? '' : ' - ' + event.end.format('h:mma')" class="cs-end-time"></span>
            </div>
            <div class="cs-location"><span x-text="event.location" class="cs-location-gliph"></span></div>
            <div class="cs-address"><span x-text="event.postcode"></span></span></div>
          </div>
        </div>
      </template>
    </div>
  </div>

EOC;

		return $output;
	}

}


/*
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
	
