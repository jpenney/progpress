=== ProgPress ===
Contributors: jczorkmid 
Donate link: http://jasonpenney.net/donate
Tags: progress, meter, bar, graph
Requires at least: 2.2
Tested up to: 2.8.3
Stable tag: 0.8.5
	
A Progress Meter Plugin for WordPress

== Description ==

I'm aware this README needs some work.  There's [an excellent write-up on using the plugin here](http://www.inkygirl.com/progpress-wordpress-plugin-for-wordcount-tracking/).

You add a progress meter by including the following in a post or in a text widget:

`<!--progpress|MeterTitle|MeterGoal|CurrentCount|PreviousCount|CountLabel-->`

or, if you want to use it directly in one of your template files (thanks to [Scott Phillips](http://scottphillips.org/blog/) for pointing out I forgot this):

`<?php if (function_exists('jcp_progpress_generate_meter')){  
       echo jcp_progpress_generate_meter("MeterTitle", MeterGoal, 
              CurrentCount, PreviousCount,"CountLabel");  
}?>`

Only MeterTitle, MeterGoal, and CurrentCount are required. The other arguments can be left off.

The appearance of the meters are controlled via css, and can be customized accordingly to allow them to better fit the feel of your site.  The default CSS can be viewed on the settings page.  Example customizations can be seen on the [plugin homepage](http://jasonpenney.net/wordpress-plugins/progpress/).
	
== Installation ==

Extract the zip file and just drop the contents in the
wp-content/plugins/ directory of your WordPress installation and then
activate the Plugin from Plugins page.

You can configure the options from the settings page.
  
