<?php
/*
Plugin Name: ProgPress
Plugin URI: http://jasonpenney.net/wordpress-plugins/progpress/
Description: Easily insert progress meters into your content and/or sidebars.
Version: 1.0
Author: Jason Penney
Author URI: http://jasonpenney.net/

Copyright 2010  Jason Penney (email : jpenney[at]jczorkmid.net )

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

$jcp_progpress_version='1.0';

define( 'PP_BASENAME', plugin_basename( __FILE__ ) );
define( 'PP_BASEFOLDER', plugin_basename( dirname( __FILE__ ) ) );
define( 'PP_FILENAME',  plugin_basename( __FILE__ ) );
define( 'PP_CSS_URL',  WP_PLUGIN_URL . '/progpress/styles/progpress_default.css' );
define( 'PP_JS_ADMIN', WP_PLUGIN_URL .'/progpress/js/admin.js' );
 


function jcp_progpress_filter( $text ) {
  $match = "/<!--progpress\|([^>]+)-->/e";
  $replace = "call_user_func_array('jcp_progpress_generate_meter',explode('|','\\1'))";
  $text = preg_replace( $match, $replace, $text );
  return $text;
}

function jcp_progpress_generate_meter( $title, $goal, $current, $previous=0,
                                       $label="", $separator='/', $class='',
                                       $prefix='' ) {

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
  $class = trim('jcp_pp ' . $class);
  
  $ret = '<div class="'.$class.'"'. 
    ($isfeed ? ' style="width: 80%; max-width:200px;margin:0 auto;padding:0;text-align:center;_width:200px;" ' :'') .'>'.
    '<div class="jcp_pp_title"'. ($isfeed ? ' style="font-weight: bold" ' : '') . '>'.$title.'</div>'.
    '<div class="jcp_pp_meter" '. jcp_progpress_generate_title($goal_label,$label) . ($isfeed ? ' style="border: 1px solid #000; height: 20px; overflow: hidden; padding: 2px; width: 100%;" ' : '') . ' >'.
    '<div class="jcp_pp_prog" '. jcp_progpress_generate_title($prog_label,$label) .' style="width:'.$current_width.'%;' . ($isfeed ? ' background-color: #000; float: left; height: 100%' : '') .'"><!--*--></div>'.
    '<div class="jcp_pp_new" ' . jcp_progpress_generate_title($new_label,$label) .  ' style="width:'.$new_width.'%;'. ($isfeed ? ' background-color: #000; float: left; height: 100%' : '') .'"><!--*--></div>'.
    '</div>'.
    '<span class="jcp_pp_count">'.
    '<span class="jcp_pp_current">' . 
    ($prefix ? '<span class="jcp_pp_prefix">'.$prefix.'</span>' : '') .
    number_format($current) . '</span>' .
    '<span class="jcp_pp_separator">' . $separator .'</span>'.
    '<span class="jcp_pp_goal">' . 
    ($prefix ? '<span class="jcp_pp_prefix">'.$prefix.'</span>' : '') .
    number_format($goal) . '</span>';
  if (strcmp("",$label) != 0) {
    $ret .= ' <span class="jcp_pp_label">' . $label . '</span>';
  }
  $ret .= '</span></div>';
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

/* shortcode */
// [progpress title="title" goal="100" current="50" previous="10" label="percent"]
function jcp_progpress_progpress_func($atts) {
  $opts = (shortcode_atts(array(
                                'title' => '',
                                'goal' => 0,
                                'current' => 0,
                                'previous' => 0,
                                'label' => '',
                                'separator' => '/',
                                'class' => '',
                                'prefix' => ''
                                ), $atts));
  return jcp_progpress_generate_meter($opts['title'],
                                      $opts['goal'],
                                      $opts['current'],
                                      $opts['previous'],
                                      $opts['label'],
                                      $opts['separator'],
                                      $opts['class'],
                                      $opts['prefix']
                                      );
  
}
add_shortcode('progpress', 'jcp_progpress_progpress_func');


/* admin */

function jcp_progpress_admin_options() { 
?>
  <div class="wrap jcp_progpress">
  <h2>ProgPress</h2>
  <form method="post" action="options.php">
  <?php 
     settings_fields('jcp_progpress_options');
     $options = get_option('jcp_progpress');    
?>
  <h3>Options</h3>
  <table class="form-table">
     <tr valign="top">
     <th scope="row">
     <label for="jcp_progpress[filter_the_content]">
     Comment-Style in Posts</label>
     </th>
     <td>
     <input type="checkbox" id="jcp_progpress[filter_the_content]" name="jcp_progpress[filter_the_content]" value="1"  <?php checked(1, $options['filter_the_content']); ?> />
     </td>
     </tr>
     <tr valign="top">
     <th scope="row">
     <label for="jcp_progpress[filter_text_widget]">
     Comment-Style in Text Widgets</label>
     </th>
     <td>
     <input type="checkbox" id="jcp_progpress[filter_text_widget]" name="jcp_progpress[filter_text_widget]" value="1" <?php checked('1',$options['filter_text_widget']); ?> />
     </td>
     </tr>
<tr valign="top">
     <th scope="row">
     <label for="jcp_progpress[shortcode_text_widget]">
     Enable Shortcodes in Text Widgets</label>
     </th>
     <td>
     <input type="checkbox" id="jcp_progpress[shortcode_text_widget]" name="jcp_progpress[shortcode_text_widget]" value="1" <?php checked('1',$options['shortcode_text_widget']); ?> />
     <span class="description">Allows use of <strong>all</strong> shortcodes in text widgets.  If you see the raw shortcode in text widgets you need to check this box.</span>
     </td>
     </tr>
     <tr valign="top">
     <th scope="row">
     <label for="jcp_progpress[include_css]">
     Use Default Styles</label>
     </th>
     <td>
     <input type="checkbox" id="jcp_progpress[include_css]" name="jcp_progpress[include_css]" value="1" <?php checked('1',$options['include_css']); ?> />
     <span class="description">Load additional style sheet containing the 
     standard ProgPress styles.</span>
     </td>
     </tr>
     </table>
     <p class="submit">
     <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />     
     </p>
     </form>
     <h3>Examples</h3>
     <a class="button-secondary" id="jcp_progpress_preview_styles" href="<?php print(PP_CSS_URL); ?>" target="_blank">Load Examples</a>
     <div id="jcp_progpress_sample_output" style="display:none" valign="top">
     <h4>Output</h4>
     <?php
     $meter_markup =  jcp_progpress_generate_meter("ProgPress Sample", 1000, 
                                                   700, 500, "things");
     echo $meter_markup; ?>
     <h4>Default Styles</h4>
     <div id="jcp_progpress_styles"></div>
     <h4>Shortcode</h4>
     <pre>[progpress title="ProgPress Sample" goal="1000" current="700" previous="500" label="things"]</pre>
     <h4>Comment-Style</h4>
     <pre>&lt;!--progpress|ProgPress Sample|1000|700|500|things--&gt;</pre>
     <h4>Generated Markup</h4>
     <span class="description"><strong>Note:</strong> Output has been 
     reformatted for readability. The <tt>&lt;--*--&gt;</tt> comments prevent 
     WordPress from removing the meters as whitespace.</span>
     <pre><?php
     $meter_src = $meter_markup;
     $meter_src = preg_replace('/(<div)/', "\n$1", $meter_src);
     $meter_src = preg_replace('/(\/div>)/', "$1\n", $meter_src);
     $meter_src = preg_replace('/([^\n])(<span)\s+/', "$1$2\n    ", 
                               $meter_src);
     $meter_src = preg_replace('/\n+/', "\n", $meter_src);

     echo htmlspecialchars($meter_src);
     ?>
     </pre>
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
  $input['filter_the_content'] = ( $input['filter_the_content'] == 1 ? 1 : 0 );
  $input['filter_text_widget'] = ( $input['filter_text_widget'] == 1 ? 1 : 0 );
  $input['include_css'] = ( $input['include_css'] == 0 ? 0 : 1 );
  $input['shortcode_text_widget'] = ( $input['shortcode_text_widget'] == 1 ? 
                                      1 : 0 );
  return $input;
}

function jcp_progpress_print_styles() {
  global $jcp_progpress_version;
  wp_register_style('jcp_progpress_styles',PP_CSS_URL,array(),
                    $jcp_progpress_version);
  wp_enqueue_style('jcp_progpress_styles');
}


function jcp_progpress_init() {


  if( !is_admin() ) {
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

    /* based on proposed change for WordPress 3.0 that was deferred */
    if ($options['shortcode_text_widget'] == 1) {
      remove_filter( 'widget_text', 'shortcode_unautop' );
      remove_filter( 'widget_text', 'do_shortcode');
      add_filter( 'widget_text', 'shortcode_unautop' );
      add_filter( 'widget_text', 'do_shortcode');      
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
