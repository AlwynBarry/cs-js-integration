<?php

namespace amb_dev\CS_JSI;


require_once plugin_dir_path( __FILE__ ) . 'class-cs-js-shortcode.php';
use amb_dev\CS_JSI\Cs_Js_Shortcode as Cs_Js_Shortcode;


/**
 * A child of Cs_Js_Shortcode to provide the creation of the HTML response for small lists
 * of events (likely less than 12) for use in a side 'widget area' or similar.
 * 
 * This class uses Alpine.js to provide the HTML to display the events as a list.
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
class Cs_Js_Event_List_Shortcode extends Cs_Js_Shortcode {

   	/*
	 * Provided to prevent continual re-creation of a constant value used in date calculations
	 */
	protected readonly \DateInterval $one_day;

    public function __construct( $atts ) {
        parent::__construct( $atts );
		$this->one_day = \DateInterval::createFromDateString( '1 day' );
	}

	/*
	 * A helper function to display the date split up into separate spans so it can be styled
	 * 
	 * @since 1.0.0
	 * @param \DateTime $event_date		The date to be displayed
	 * @result	string					The date split into html spans for day, date number, month and year
	 */
	protected function display_event_date( \DateTime $event_date ) : string {
	    $result = '<div class="cs-date">';
		$result .= '<span class="cs-day">' . $event_date->format( 'D' ) . '</span>';
		$result .= '<span class="cs-date-number">' . $event_date->format( 'd' ) . '</span>';
		$result .= '<span class="cs-month">' . $event_date->format( 'M' ) . '</span>';
		$result .= '<span class="cs-year">' . $event_date->format( 'Y' ) . '</span>';
		$result .= '</div>';
		return $result;
	}

	/*
	 * Use the JSON response to create the HTML containing the list of events.
	 * 
	 * For each date there is only one date output in a left hand column, styled, and then
	 * in the corresponding right hand columns we have each event on that date.
	 * 
	 * @since	1.0.0
	 * @return	string	the HTML to render the events in cards
	 */
	protected function get_HTML_response() : string {

		$output = <<<EOC
  <!-- Tell it which configuration to use... -->
  <div x-data="CSEvents({configuration: '$this->configuration'})">
    <div class="cs-event-list">
      <template x-for="event in events">
        <!-- There can only be one element within the template -->
        <div class="cs-event-list-event">
          <div class="cs-event-row">
            <div class="cs-date-column">
              <div class="cs-date">
                <!-- Event times are day.js instances - see https://day.js.org/ for formatting options -->
		        <span x-text="event.start.format('ddd')" class="cs-day"></span>
		        <span x-text="event.start.format('D')" class="cs-date-number"></span>
		        <span x-text="event.start.format('MMM')" class="cs-month"></span>
		        <span x-text="event.start.format('YYYY')" class="cs-year"></span>
		      </div>
		    </div>
		    <div class="cs-event-column">
		      <div :id="event.identifier" :class="event.status" class="cs-card cs-compact-event">
                <div class="cs-time">
                    <span x-text="event.allDay ? 'All Day' : event.start.format('h:mma')" class="cs-time-gliph cs-start-time"></span>
                    <span x-text="event.allDay ? '' : ' - ' + event.end.format('h:mma')" class="cs-end-time"></span>
                </div>
                <div class="cs-event-name">
                  <a :href="event.link" class="cs-event-link" target="_blank"><span x-text="event.name"></span></a>
                </div>
                <div class="cs-location"><span x-text="event.location" class="cs-location-gliph"></span></div>
                <div class="cs-address"><span x-text="event.postcode"></span></span></div>
              </div>
            </div>  
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
function cs_js_event_list_shortcode( $atts ) {
	return ( new Cs_Js_Event_List_Shortcode( $atts ) )->run_shortcode();
}
	
