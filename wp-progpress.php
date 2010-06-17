<?php
/*
Plugin Name: ProgPress
Plugin URI: http://jasonpenney.net/wordpress-plugins/progpress/
Description: CSS Based progress meters
Version: 0.8.6
Author: Jason Penney
Author URI: http://jasonpenney.net/

Copyright 2007  Jason Penney (email : jpenney@jczorkmid.net )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation using version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	     
*/

$jcp_progpress_version='0.8.6';

define( 'PP_BASENAME', plugin_basename( __FILE__ ) );
define( 'PP_BASEFOLDER', plugin_basename( dirname( __FILE__ ) ) );
define( 'PP_FILENAME', PP_BASEFOLDER.'/'. plugin_basename(__FILE__) );
define('PP_CSS_URL',  WP_PLUGIN_URL . '/progpress/styles/progpress_default.css');
define('PP_JS_ADMIN', WP_PLUGIN_URL .'/progpress/js/admin.js');
 


function jcp_progpress_filter($text) {
  $match = "/<!--progpress\|([^>]+)-->/e";
  $replace = "call_user_func_array('jcp_progpress_generate_meter',explode('|','\\1'))";
  $text = preg_replace($match, $replace, $text);
  return $text;
}

function jcp_progpress_generate_meter($title,$goal,$current,$previous=0,$label="") {

  if ($previous == '') {
    $previous = 0;
  }
  /* avoid divide by zero */
  if ($goal == 0) {
    return '';
  }
  $goal_label = "Goal: " . $goal;
  $prog_label = $current;
  $new_label = '';	
  $new_width = 0;
  if ($previous > 0) {
    $new = $current - $previous;
    $new_width = (int)(($new/$goal)*100);
    $current_width = (int)(($previous/$goal)*100);
    if ($new_width + $current_width != 100) {
      $new_width++;
    }
    $prog_label = $previous;
    $new_label = $new;
  } else {
    $current_width= (int)(($current/$goal)*100);
  }
  $isfeed = is_feed(); 
  $ret = '<div class="jcp_pp"'. ($isfeed ? ' style="width: 80%; max-width:200px;margin:0 auto;padding:0;text-align:center;_width:200px;" ' :'') .'>'.
    '<div class="jcp_pp_title"'. ($isfeed ? ' style="font-weight: bold" ' : '') . '>'.$title.'</div>'.
    '<div class="jcp_pp_meter" '. jcp_progpress_generate_title($goal_label,$label) . ($isfeed ? ' style="border: 1px solid #000; height: 20px; overflow: hidden; padding: 2px; width: 100%;" ' : '') . ' >'.
    '<div class="jcp_pp_prog" '. jcp_progpress_generate_title($prog_label,$label) .' style="width:'.$current_width.'%;' . ($isfeed ? ' background-color: #000; float: left; height: 100%' : '') .'"><!--*--></div>'.
    '<div class="jcp_pp_new" ' . jcp_progpress_generate_title($new_label,$label) .  ' style="width:'.$new_width.'%;'. ($isfeed ? ' background-color: #000; float: left; height: 100%' : '') .'"><!--*--></div>'.
    '</div>'.
    '<span class="jcp_pp_count">' . $current . '/' . $goal;
  if (strcmp("",$label) != 0) {
    $ret .= ' ' . $label;
  }
  $ret .= '</span>' . '</div>';
  return $ret;
} 

function jcp_progpress_generate_title($value,$label) {
  $ret = '';
  if (strcmp('',$value) != 0) {
    $ret = 'title="' . $value;
    if (strcmp('',$label) != 0) {
      $ret .= " " . $label;
    }
    $ret .= '"';
  }
  return $ret;
}


/* admin */

function jcp_progpress_admin_options() { 
?>
  <div class="wrap jcp_progpress">
  <h2>ProgPress Options</h2>
  <form method="post" action="options.php">
  <?php 
     settings_fields('jcp_progpress_options');
     $options = get_option('jcp_progpress');    
?>
     <p class="submit">
     <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />     
     </p>
  <table class="form-table">
     <tr valign="top">
     <th scope="row">
     <label for="jcp_progpress[filter_the_content]">
     Display progress meters in posts:</label>
     </th>
     <td>
     <input type="checkbox" id="jcp_progpress[filter_the_content]" name="jcp_progpress[filter_the_content]" value="1"  <?php checked(1, $options['filter_the_content']); ?> />
     </td>
     </tr>
     <tr valign="top">
     <th scope="row">
     <label for="jcp_progpress[filter_text_widget]">
     Display progress meters in Text Widgets:</label>
     </th>
     <td>
     <input type="checkbox" id="jcp_progpress[filter_text_widget]" name="jcp_progpress[filter_text_widget]" value="1" <?php checked('1',$options['filter_text_widget']); ?> />
     </td>
     </tr>
     <tr valign="top">
     <th scope="row">
     <label for="jcp_progpress[include_css]">
     Use build-in styles:</label>
     </th>
     <td>
     <input type="checkbox" id="jcp_progpress[include_css]" name="jcp_progpress[include_css]" value="1" <?php checked('1',$options['include_css']); ?> />
     </td>
     </tr>
     <tr valign="top">
     <th scope="row">
     <a id="jcp_progpress_preview_styles" href="<?php print(PP_CSS_URL); ?>" target="_blank">View Default Styles:</a>
     </th>
     <td>
     </td>
     </tr>
     <tr id="jcp_progpress_sample_output" style="display:none" valign="top">
     <th scope="row">Sample Output:</th>
     <td><h4>Markup</h4><pre style="display: block; margin: 0 auto;text-align:center;">&lt;!--progpress|ProgPress Sample|1000|700|500|Label--&gt;</pre>
     <h4>Output</h4>
     <?php echo jcp_progpress_generate_meter("ProgPress Sample", 1000, 700, 500, "Label"); ?>
     </td>
     </tr>
     </table>
     <p class="submit">
     <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />     
     </p>
     </form>
     </div>
     <?php
}

function jcp_progpress_modify_menu() {
  add_options_page('ProgPress Options','ProgPress', 8,
                   PP_BASENAME,
                   'jcp_progpress_admin_options');
}

function jcp_progpress_admin_init() {
  global $jcp_progpress_version;
  register_setting('jcp_progpress_options', 'jcp_progpress',
                   'jcp_progpress_options_validate');
  wp_register_script('jcp_progpress_admin', PP_JS_ADMIN, array('jquery'),
                     $jcp_progpress_version);
  wp_enqueue_script('jcp_progpress_admin');
    
}

function jcp_progpress_activation() {
  $options = jcp_progpress_options_validate();
  // if old options exist, update to new system
  foreach( $new_options as $key => $value ) {
    if( $existing = get_option( 'jcp_pp_' . $key ) ) {
      $options[$key] = $existing;
      delete_option( 'jcp_pp_' . $key );
    }
  }
  add_option('jcp_progpress', $new_options);
};



function jcp_progpress_options_validate($input) {
  $input['filter_the_content'] = ( $input['filter_the_content'] == 1 ? 1 :0);
  $input['filter_text_widget'] = ( $input['filter_text_widget'] == 1 ? 1 :0);
  $input['include_css'] = ( $input['include_css'] == 1 ? 1 :0);
  return $input;
}

function jcp_progpress_print_styles() {
  global $jcp_progpress_version;
   wp_register_style('jcp_progpress_styles',PP_CSS_URL,array(),
                    $jcp_progpress_version);
  wp_enqueue_style('jcp_progpress_styles');
}


function jcp_progpress_init() {


  if(!is_admin()) {
    $options = get_option('jcp_progpress');
    if ($options['filter_the_content'] == 1) {
       add_filter('the_content','jcp_progpress_filter',100);
    }
       
    if ($options['filter_text_widget'] == 1) {
       add_filter('widget_text','jcp_progpress_filter',100);
    }

    if ($options['include_css'] == 1) {
      // high priority to ensure theme styles can override more easily
      add_action('wp_print_styles', 'jcp_progpress_print_styles', 1);
    }
  }
}

function jcp_progpress_row_meta($links, $file) {
  if ($file == PP_BASENAME) {
    $links = jcp_progpress_action_links($links);
  }
  return $links;
}

function jcp_progpress_action_links($links) {
    array_unshift($links, 
                  sprintf('<a href="options-general.php?page=%s">%s</a>', 
                          PP_FILENAME, __('Settings')));
    return $links;
}

if (function_exists('plugin_row_meta')) {
  add_filter('plugin_row_meta','jcp_progpress_row_meta');
} else {
  add_filter('plugin_action_links_'.PP_BASENAME,'jcp_progpress_action_links');
}



register_activation_hook(__FILE__,'jcp_progpress_activation');
add_action('admin_init','jcp_progpress_admin_init');
add_action('admin_menu','jcp_progpress_modify_menu');
add_action('init','jcp_progpress_init');
