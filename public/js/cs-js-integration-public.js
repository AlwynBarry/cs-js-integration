/**
 * Name: csjs-integration
 * 
 * Descripton: Object that provides the functionality required to display the calendar in Alpine.js
 * 
 * @author		Alwyn Barry
 * @copyright	2025
 * For use in	csjs-integration
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @version		1.0.0
 * 
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License as published by the Free Software Foundation; either version 2 of the License, 
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * 
 */
 
 
/**
 * Remove all the HTML tags in a string
 *
 * @since	1.0.0
 * @param   string  str The string to be sanitized
 * @return  string      The string with all HTML tags removed
 */
function csjs_remove_tags( str ) {
	if ( (str === null) || (str === '') )
		return '';
	else
		str = str.toString();
	return str.replace(/(<([^>]+)>)/ig, '');
}


/**
 * Sanitize all the HTML in a string
 *
 * @since	1.0.0
 * @param   string  str The string to be sanitized
 * @return  string      The string with all HTML tags removed
 */
function csjs_sanitize_HTML( str ) {
  var element = document.createElement('div');
  element.innerText = str;
  return element.innerHTML;
}


/**
 * A x-data object to maintain the date of the request for the calendar and provide
 * utility functions and filters that enable the calendar to be rendered correctly
 * 
 * @since   1.0.0
 */
function csjs_calendar_app() {

    return {

        /**
         * The dayjs instance holding today's date - allows the date to be highlighted
         *
         * @since   1.0.0
         * @var     dayjs	today	Holds today's date
         */
        today: '',

        /**
         * The month number (0 - 11) of the month currently being displayed.
         * Initially this is set to the month number of the date in the month
         * requested as the start date.  This is updated by the forward and back
         * buttons to a different month, as desired by the viewer.
         *
         * @since   1.0.0
         * @var     int	  month	Holds the month number (0..11) of the
         *                      base date of the calendar being displayed
         */
        month: '',

        /**
         * The month name of the month currently being displayed.
         * This is kept in sync with the change in the month number as long as
         * get_days() is called to fetch the new month data
         *
         * @since   1.0.0
         * @var     string	month_name	Holds the string name of the current month
         */
        month_name: '',

        /**
         * The year number.  This is initially set to today's year. We hold this
         * so that we can conveniently create dates for comparison purposes.
         *
         * @since   1.0.0
         * @var     int year    Holds the year number of the base date of the
         *                      calendar being displayed
         */
        year: '',
        
        /**
         * The day number.  This is initially set to today's day number.
         *
         * @since   1.0.0
         * @var     int day     Holds the day number (1..31) of the base date
         *                      of the calendar being displayed
         */
        day: '',
        
        /**
         * An array holding 5 weeks worth of date stings in YYYY-MM-DD format
         * which we iterate over to output the events for each date.  The dates
         * are re-written by calling function get_days() after the year, month,
         * or day attributes are set to get five weeks which will include the
         * whole week the initial date is in and the subsequent four weeks.
         *
         * @since   1.0.0
         * @var     array   days  An array of 35 dates from the start of the week
         *                        of the date being displayed until the end of the
         *                        week five weeks later.
         */
        days: [],
        
        /**
         * An array of day names in the localized format. This is a local copy
         * of the day names that the ChurchSuite API creates. This array is used
         * to display the days at the top of the calendar.  The days are arranged
         * to always start from Sunday.
         *
         * @since   1.0.0
         * @var     array   day_names   Holds seven localized names of the days
         *                              of the week starting from Sunday.
         */
        day_names: [],
        

        /**
         * Construct the initial values from today's date.
         *
         * @since	1.0.0
         */
        init_date() {
            /*
             * Set the date values from today's date using a dayjs date
             */
            this.today = dayjs();
            this.year = this.today.get( 'year' );
            this.month = this.today.get( 'month' );
            this.month_name = this.today.format( "MMMM" );
            this.day = this.today.get( 'date' );
            /*
             * Set the day names to the internationalised day names ChurchSuite provides
             */
            let cs_day_names = CS.dayOfWeekOptions();
            this.day_names.push( cs_day_names[6].name );
            for ( var i = 0; i < 6; i++ ) {
                this.day_names.push( cs_day_names[i].name );
            }
        },


        /**
         * Check if a dayjs date supplied is before today's date
         *
         * @since	1.0.0
         * @param   dayjs   date    The date to be checked
         * @return  bool            True if the year, month, date part of the date
         *                          is prior to that of the current date (ignore time)
         */
        is_before_today( date ) {
            return date.isBefore( this.today );
        },


        /**
         * Check if a dayjs date supplied is equal to today's date
         *
         * @since	1.0.0
         * @param   dayjs   date    The date to be checked
         * @return  bool            True if the year, month, date part of the date
         *                          is the same as that of today's date
         */
        is_today( date ) {
            return date.isSame( this.today, 'day' ) ? true : false;
        },

        
        /**
         * Check if a ChurchSuite Event object is on the same date as the date
         * being represented by the filter object passed as a parameter.
         *
         * @since	1.0.0
         * @param   Event   ev  The ChurchSuite Event object to be checked
         * @param   dayjs   d   The current date being processed in the filter        
         * @return  bool        True if the event is the same date as the filter date d
         */
        equals_date_filter( ev ) {
            /* NOTE: this.d is the filter object, NOT an attribute of this class */
            const other = new dayjs( this.d );
            return ev.start.isSame( other, 'day' ) ? true : false;
        },

        
        /**
         * Construct an array of the days in the five weeks from the week which
         * includes the current day.  The array contains date strings in the format
         * 'YYYY-MM-DD' for each day of the five weeks, beginning on Sunday.
         *
         * @since	1.0.0
         * @return  array    Array of 35 strings, each with format 'YYYY-MM-DD'
         *                   which are the dates of the days from the start of the
         *                   week of the current date and on for 35 days (5 weeks)
         */
        get_days() {
            let origin_date = dayjs( this.year + '-' + ( this.month + 1 ) + '-' + this.day );
            let day_of_week = origin_date.day();
            let start_date = origin_date.subtract( day_of_week, 'day' );
            let days_to_display = 35; // Multiple of 7 - default is 5 weeks

            let days_array = [];
            for ( var i = 0; i < days_to_display; i++ ) {
                days_array.push( start_date.format( 'YYYY-MM-DD' ) );
                start_date = start_date.add( 1, 'day' );
            }
            this.month_name = origin_date.format( 'MMMM' );
            this.days = days_array;
        }

    }
}


/**
 * A x-data object to maintain the date of the request for the event list
 * and provide utility functions so that each event can be checked to see
 * when a new date has been reached.
 * 
 * @since   1.0.0
 */
 function csjs_event_list_app() {

    return {

        /**
         * The dayjs instance holding the date of the current item being
         * processed, initialised to a date before the first event.
         * Used to work out when we have a first occurance of a new date
         *
         * @since   1.0.0
         * @var     dayjs	current_date	Holds the day being processed
         */
        current_date: '',

        /**
         * Construct the initial values from today's date.
         *
         * @since	1.0.0
         */
        init_date() {
            /*
             * Set the date values from today's date using a dayjs date
             */
            this.current_date = dayjs().subtract( 1, 'day' );
        },
        
        /**
         * Check if a dayjs date supplied is on the previous date stored
         * As a side effect, if the date is different, update the date
         * recorded to the date of the current event.
         *
         * @since	1.0.0
         * @param   dayjs   date			The date to be checked
         * @return  bool					True if the year, month, date part of the date
         *                          		is the same as that of the stored date
         * @state	datejs	current_date	Updated to the date of the event if
         * 									the current_date is an earlier date.
         */
        is_same( date ) {
            let result = date.isSame( this.current_date, 'day' ) ? true : false;
            if ( ! result ) {
				// Set the current date to the date passed in
				this.current_date = date;
			}
			return result;
        }

    }
}

