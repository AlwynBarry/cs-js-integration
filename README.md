# Javascript Integration for ChurchSuite
* Contributors: Alwyn Barry
* Tags: Events, ChurchSuite, Featured
* Requires at least: 6.4
* Tested up to: 6.7.2
* Stable tag: 1.0.0
* License: GPLv2 or later

JS Integration for ChurchSuite is provides ChurchSuite event and smallgroup
details for display in a Wordpress website.

## Description

JS Integration for ChurchSuite is a plugin to provide ChurchSuite event and smallgroup
details for display in a Wordpress website without using iFrames.  The data is drawn
from ChurchSuite JSON feeds and is displayed natively within your website using Javascript.
This plugin uses the ChurchSuite Javascript public API library to implement much of its
functionality, but needs a **lot less** technical understanding to use in a WP website
than trying to integrate the libraries and write it all from scratch.


## Difference between this plugin and cs-integration

We also provide the `cs-integration` plugin.  That plugin uses and older ChurchSuite API
which does permit much more flexibility because a range of parameters can be provided at
call rather than having to create a ChurchSuite 'embed configuration' to pass in the
shortcode call.  It also does all the work on the 'server side' so that the server holds
the cached data and the server creates all the html for output.  This plugin use Javascript
to create the response and cache in the client browser.  The server-side implementation
can be faster for many repeated requests, and is less speed dependent on the client provision.
However the client-side implementation can be faster for an individual user. This
plugin also uses Alpine.js to output the HTML, which means an end user could change the
output by changing the HTML files without having to get into the php of the plugin. However,
the Alpine.js code isn't straightforward and so this is likely to be of little advantage.
Really, it's simply 'horses for courses' - you have the choice of which to use!


## Current features include:

* Shortcode to return events as 'cards' with the event image and details
* Shortcode to return events in a 'list' grouped by date
* Shortcode to return a Calendar which can move on to subsequent months where needed
* Shortcode to return groups as 'cards' with the group image and details
* All API requests are cached locally to speed performance 


## Support

If you have a problem or a feature request, please send a message to the author.


## Demo

Currently there is no demo site.


## Contributions

This plugin relies on the Churchsuite Javascript API library
   (see [https://github.com/ChurchSuite/embed-json-script]https://github.com/ChurchSuite/embed-json-script)
This plugin uses Alpine.js to process the ChurchSuite events and smallgroup information for display
   (see [https://alpinejs.dev/]https://alpinejs.dev/)
This plugin uses dayjs to process dates, because this is what the ChurchSuite API uses
   (see [https://dayjs.org]https://dayjs.org/)


# Installation

* From within Wordpress - In the Wordpress Dashboard use the menu to go to
  Plugins and from there choose 'Add new plugin'.  Search for 'churchsuite'
  and then look for this plugin.  Select the 'install' button on the plugin
  to install it, and once installed use the 'activate' link to activate the
  plugin.

* If you want to install from github:

	- Download from 'releases'
	- Rename the zip file downloaded 'cs-js-integration.zip' (i.e. remove any
	  version info in the filename)
	- In Wordpress use the Install New Plugin page to upload the zip file, or
	  alternatively, unpack and upload the cs-integration directory to your
	  '/wp-content/plugins/' directory.
	- Once you have done either of the above, Activate the plugin through the
	  'Plugins' page in the WordPress dashboard.

* Once you have used either method to install the plugin, you need to then
  add a shortcode (see examples below) to your wordpress posts or pages where
  you need them

## Usage
* For each of the examples below:
    - Replace `mychurch` with the name of your church which you use to get to your ChurchSuite site;
e.g. `trinity` or `christchurch` - see the first name after `https://` in your Churchsuite link.
    - Replace `62436903-841e-4239-bc95-e6952e17430e` with the unique Id for the configuration of event
or SmallGroup output you want to use. To easily find this Unique ID, go to the Settings for Events
or SmallGroups, and use the Embed tab to see the Configurations (or create new Configurations),
and use 'Preview' to show the configuration you want to base the output on.  The Unique ID is the
string of hexadecimal characters you will find in the Link for the preview page.
* For the *Event Cards shortcode*, place the shortcode into a page or post or into a shortcode block.
    The shortcode will be: `[cs-js-event-cards church_name="mychurch" configuration="62436903-841e-4239-bc95-e6952e17430e"]`
(replacing the church name and the configuration with your church name and the Embed configuration
unique ID you want to use)
* For the *Event List shortcode*, place the shortcode into a page or post or into a shortcode block.
    The shortcode will be: `[cs-js-event-list church_name="mychurch" configuration="62436903-841e-4239-bc95-e6952e17430e"]`.
(replacing the church name and the configuration with your church name and the Embed configuration
unique ID you want to use)
* For the *Smallgroups shortcode*, place the shortcode into a page or post or into a shortcode block.
    The shortcode will be: `[cs-js-smallgroups church_name="mychurch" configuration="62436903-841e-4239-bc95-e6952e17430e"]`
(replacing the church name and the configuration with your church name and the Embed configuration
unique ID you want to use)

Sadly, ChurchSuite developers won't allow us to use any parameters inline to modify what data
is received.  You can use the (very limited) configuration available in the Embed Configuration
if you wish.  The 'look and feel' of the data as presented on your website can be altered
considerably by adding your own CSS to your theme file - all items output are within their own
css classes and so everything can be styled to fit with your website.

And, if you want to, you can use an HTML block to do your own output using Alpine.js.  Just
follow the examples towards the bottom of https://kingshope.church/events. The libraries you
need have been impported for you by this plugin, and so, for example, a simple unstyled list
of event titles can be generated by adding an HTML block with:

```
<script>CS.url = 'https://demo.churchsuite.com';</script>

<div>
  <!-- Tell it which configuration to use... -->
  <div x-data="CSEvents({configuration: '62436903-841e-4239-bc95-e6952e17430e'})">
    <!-- ... and then get designing! -->
    <template x-for="event in events">
      <!-- There can only be one element within the template -->
      <div>
        <span x-text="event.name"></span>
      </div>
    </template>
  </div>
</div>
```
(replacing `demo` with your church name, and the hex string with your configuration unique id).


The plugin itself is released under the GNU General Public License. A copy of this license
can be found at the license homepage or in the `cs-integration.php` file in the top comment.


# Frequently Asked Questions

- The shortcode produces no output

    Check that you have supplied the correct churchname and the correct configuration id.
    You can check this by entering the following URL in a browser with your church name
    and the configuration id you are trying to use:

	https://demo.churchsuite.com/-/calendar/62436903-841e-4239-bc95-e6952e17430e/json

- How do I add my church so that I get the JSON feed for my church?

    You must use the shortcode `church_name` parameter:

	[cs-js-event-cards church_name="mychurch" configuration="62436903-841e-4239-bc95-e6952e17430e"]

- I want to limit the number of events in the shortcode.

You have to modify any limits on number of events through the Configuration on ChurchSuite

- I want to change how the output looks:

    The output is formatted via css - just override the defaults in your theme

- I want to write a different shortcode

    That is actually really easy to do using our shortcodes as a base.  But its too long
    to explain how to here - contact the plugin author.

# Screenshots

None as yet


# Changelog

**2025-03-21**
* Added Calendar shortcode and tidied up all the css styling across the shortcodes

**2025-03-11**
* Initial release of a usable small set of shortcodes, given the big limitations of the
  new Churchsuite JSON API.  All commented and made ready for future submission to the
  Wordpress Plugin Directory.
