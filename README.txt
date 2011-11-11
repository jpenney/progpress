=== ProgPress ===
Contributors: jczorkmid 
Donate link: http://jasonpenney.net/donate
Tags: progress, meter, bar, graph, wordcount, word meter, goal, tracking, counter, thermometer, writing, fundraiser, donations, NaNoWriMo
Requires at least: 2.2
Tested up to: 3.3
Stable tag: 1.2.1
	
Easily insert progress meters into your content and/or sidebars.

== Description ==

*ProgPress* provides a simple
[shortcode](http://codex.wordpress.org/Shortcode) for creating
progress meters on your WordPress site.  They can be used to track
just about anything where you count up towards a goal: word-count,
fundraising, etc.  You can put them in individual posts and pages, or
include them in text widgets.

To insert a meter simply use the `[progpress]` shortcode:

`
[progpress title="My Project" goal="100000" current="1234"]
`

The `[progpress]` shortcode has the following options:

* Required:
  * **`title`**: A string containing The title for your meter.
  * **`goal`**: A number.  The one you are working towards.
  * **`current`**: A number showing how far along you are.
* Optional:
  * **`previous`**: You can put your previous value of `current` here
    if you want to highlight your most recent progress update.  It's
    not visible using the default styling (but it doesn't hurt
    anything). 
  * **`label`**: What it is that you're counting, like `"words"` for
    example.
  * **`separator`**: A character or string to display between
    `current` and `goal`. Defaults to `"/"`.
  * **`class`**: An extra CSS class to apply to the meter container.
    Useful if you track different projects with different styles.
  * **`prefix`**: A character or string to display before each number
    (like '$'). Off by default.

*ProgPress* was designed to be customizable via CSS.  I've provided
some examples in the *Screenshots* section. 

== Installation ==

Extract the zip file and just drop the contents in the
`wp-content/plugins/` directory of your WordPress installation and then
activate the Plugin from Plugins page.

You can configure the options from the settings page.
  
== Frequently Asked Questions ==

= I'm seeing the shortcode source when I try and use it in a text widget. =

As of this writing WordPress does not support shortcodes in text
widgets. *ProgPress* has the ability to enable all shortcodes in
text widgets (not just `[progpress]`), but it is turned off by
default.  It can be enabled on the *ProgPress* settings page.

The code is based on a proposed patch to WordPress core. Hopefully
this feature is added to WordPress itself at some later date. I've
tried to code defensively so if the feature is added to WordPress in
the future, having it enabled in the plugin should not have any negative
side effects. 

= What are the default styles?  What does the markup look like? =

Please check out the **Examples** section on the settings page.  It
will generate markup and display the default styles for the currently
installed version of *ProgPress* when you click "Load Examples".  I've
done this rather than put a static copy here that I might forget to
update in future.

= What's all this about shortcodes?  I've been using ProgPress in a different way for ages! =

Since *ProgPress* pre-dates shortcodes being added to WordPress it
originally used an older syntax based on HTML comments. This syntax is
still supported (although newer features like `label` have not been
back-ported).  It can be enabled on the *ProgPress* settings page. 

You add a progress meter by including the following in a post or in a
text widget: 

`
<!--progpress|title|goal|current|previous|label-->
`

Only these five options are supported, only the first three are
required.

= Besides shortcodes and HTML comments, is there any other way I can use ProgPress to generate meters? =

Sure.  You can call the `jcp_progpress_generate_meter` function
directly.

`<?php if (function_exists('jcp_progpress_generate_meter')){  
       echo jcp_progpress_generate_meter("title", 100, 50, 25, "label");
}?>`

The arguments to the function are: 
`title`, `goal`, `current`, `previous`, `label`, `separator`, `class`, 
and `prefix` (in that order).

= How will the meter show up in my RSS feeds? =

Inline styles (based on the default styles) are used to ensure the
meters render in RSS readers.

== Screenshots ==

1. This meter was generated using the default styles. 

    `
    [progpress title="defaults" goal="180000" current="73023" previous="71398" label="words"]
`

2. Make the meter blue and remove padding.

    `div.jcp_pp_meter { 
    padding: 0; 
}
div.jcp_pp_prog { 
    background-color: #22a; 
}
div.jcp_pp_new { 
    background-color: #22f; 
}`
    
     `
[progpress tile="Test" goal="50000" current="48000"]
`

3. Blue meters with 3D borders.

    `div.jcp_pp_meter { 
   padding: 0;
   height: 30px; 
   border: inset 4px #aaa; 
   background-color: #aaa; 
   z-index: 1; 
   overflow: visible; 
}
div.jcp_pp_prog, div.jcp_pp_new { 
    border: outset 4px red; 
    margin-left: -4px; 
    margin-top: -4px; 
    z-index: 2; 
    background-color: #22a; 
    border-color:#22a;
}
div.jcp_pp_new { 
    border-color:#22f; 
    background-color: #22f; 
    border-left: none; 
    z-index: 3; 
}`

    > `[progpress title="Test Too" goal="100000" current="70000" previous="67000"]`

4. Image Based 

    `div.jcp_pp_meter { 
    padding: 0; 
    height: 33px; 
    background-image: url(pp_bg_remain.jpg); 
}
div.jcp_pp_prog,div.jcp_pp_new {
   background-image:url(pp_bg_current.jpg); 
}`
    
     `
[progpress title="Test Also" goal="500" current="350" label="pages"]
`

== Changelog ==

= 1.2.1 =

* Fixed caching timeout (whoops)

= 1.2 =

* Updated the *ProgPress - NaNoWriMo Support* plugin for 2011
* Pretty printing of numbers now handled via `number_format_i18n` for better
  international support.
* Admin JavaScript should only load on the ProgPress admin page.

= 1.1 =

* Added the *ProgPress - NaNoWriMo Support* plugin
* Added `jcp_progpress_shortcode_atts` filter
* Added phpdoc

= 1.0 =

* Added the `[progpress]` shortcode.
* Pretty print large numbers (inserting commas, etc).
* Added `class`, `prefix`, and `separator` options
* Added option to enable shortcodes in text widgets.
* Rewrote the README (hopefully it's more helpful now).
* Added some extra tags to the markup to make it easier to style
  individual sections.

= 0.8.6 =

* fixed bug with width calculation
* provide production and development versions of javascript and css

= 0.8.5 =

* Migrated to new WordPress admin API.
* Moved default styles to external style sheet.
* Added ability to view default style sheet to settings page.
* Added a bit more content to the README.

= 0.8.2 = 

* Use inline styles in RSS feeds.
* Changed filter priorities to deal with WordPress changes.

= 0.8.1 =

* Added `title` attributes to meter sections.
* Better path determination logic.

= 0.8 =

* Removed try/catch in place of PHP version detection.
* Updated styles

= 0.1 =

* initial release

== Upgrade Notice ==

= 1.1 =
This version adds a new plugin for NaNoWriMo.  Just use [progpress
nanowrimo=*uid*] to track your progress!

= 1.0 =
This version adds the [progpress] shortcode, option to enable (all)
shortcodes in text widgets, pretty printing of large numbers, optional
prefix ($, etc.), markup tweaks to allow more styling flexibility, and
more. Upgrade today!


== NaNoWriMo Support ==

If you are participating in NaNoWriMo, ProgPress can automatically
track your progress.  Just enable the additional *ProgPress -
NaNoWriMo Support* plugin (in addition to ProgPress), and set the
`nanowrimo` attribute to your NaNoWriMo *username* (this is a change
from last year where your user id was used).

`
[progpress title="My NaNoWriMo Progress" nanowrimo="jczorkmid" label="words"]
`

So as to not overload their servers, the plugin caches your word count
info, so it may not update immediately when you update your word count
at nanowrimo.org.

Note that as of November 10, 2011 the NaNoWriMo Word Count API is
not yet officially released.  At times it seems to return invalid
errors saying your user doesn't exist, or you don't have a novel in
progress this year.  In my testing this clears itself up after a bit,
and once some data has been loaded WordPress will cache it rather than
continue to display errors if it keeps going up and down.

== More Info ==

* Check out my [other WordPress
  plugins](http://jasonpenney.net/wordpres-plugins/).
* Check out this excellent [ProgPress Setup & Customization
  Guide](http://www.penrefe.com/2010/09/07/progpress-setup-customisation-guide/)
  for some additional CSS examples. 

== Thanks ==

Special thanks to [Kris Johnson](http://kjtoo.com/),
[K. L. Kerr](http://www.penrefe.com/), [Chris
Miller](http://ctmiller.net/), [Debbie Ohi](http://www.inkygirl.com/),
and [Scott Philips](http://scottphillips.org/) for their feedback and
support. 
