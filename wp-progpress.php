<?php
/*
Plugin Name: ProgPress
Plugin URI: http://jasonpenney.net/
Description: CSS Based progress meters
Version: 0.1
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



function jcp_progpress_filter($text) {
  //$match = "/<!--progpress\|([^\|]+)\|(\d+)\|(\d+)(\|(\d+))?(\|([^\|]+))?-->/e";
  $match = "/<!--progpress\|([^>]+)-->/e";
#  $replace = "jcp_generate_progpress_meter('\\1','\\2','\\3','\\4','\\5')";
  $replace = "call_user_func_array(jcp_progpress_generate_meter,explode('|','\\1'))";
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

  $new_width = 0;
  if ($previous > 0) {
    $new = $current - $previous;
    $new_width = (int)(($new/$goal)*100) ;
    $current_width = (int)(($previous/$goal)*100);
  } else {
    $current_width= (int)(($current/$goal)*100);
  }
  
  $ret = '<div class="jcp_pp">'.
    '<div class="jcp_pp_title">'.$title.'</div>'.
    '<div class="jcp_pp_meter">'.
    '<div class="jcp_pp_prog" style="width:'.$current_width.'%"><!--*--></div>'.
    '<div class="jcp_pp_new" style="width:'.$new_width.'%"><!--*--></div>'.
    '</div>'.
    '<span class="jcp_pp_count">' . $current . '/' . $goal;
  if (strcmp("",$label) != 0) {
    $ret .= ' ' . $label;
  }
  $ret .= '</span>' . '</div>';
  return $ret;
} 


/* options */

function jcp_progpress_set_options() {
  add_option('jcp_pp_filter_the_content','on','Display progress meters in posts');
  add_option('jcp_pp_filter_text_widget','on','Display progress meters in Text Widgets');
  add_option('jcp_pp_include_css','on','Use built-in styles');
}

function jcp_progpress_unset_options() {
  delete_option('jcp_pp_filter_the_content');
  delete_option('jcp_pp_filter_text_widget');
  delete_option('jcp_pp_include_css');
}

/* admin */

function jcp_progpress_admin_options() { 
  echo '<div class="wrap"><h2>ProgPress Options</h2>';
  
  if($_REQUEST['submit']) {
    jcp_progpress_update_options();
  }
  jcp_progpress_print_options_form();
  echo '</div>';
}

function jcp_progpress_modify_menu() {
  add_options_page(
		   'ProgPress',
		   'ProgPress',
		   5,
		   __FILE__,
		   'jcp_progpress_admin_options'
		   );
}

function jcp_progpress_update_options() {

  try {
    $filter_the_content = '';
    $filter_text_widget = '';
    $include_css = '';
    
    if ($_REQUEST['jcp_pp_filter_the_content']) {
      $filter_the_content = 'on';
    }
    if ($_REQUEST['jcp_pp_include_css']) {
      $include_css = 'on';
    }
    if ($_REQUEST['jcp_pp_filter_text_widget']) {
      $filter_text_widget = 'on';
    }
    update_option('jcp_pp_filter_the_content',
		  $filter_the_content);
    update_option('jcp_pp_filter_text_widget',
		  $filter_text_widget);
    update_option('jcp_pp_include_css',
		  $include_css);
    echo '<div id="message" class="updated fade"><p>Options saved.</p></div>';
  } catch(Exception $e) {
    echo '<div id="message" class="error fade"><p>'.
      'Failed to update options</p></div>';
  }
}

function jcp_progpress_print_options_form() {
  $checked = " checked ";
  $filter_the_content_checked = "";
  $filter_text_widget_checked = "";
  $include_css_checked = "";
  if (get_option('jcp_pp_filter_the_content') != '') {
    $filter_the_content_checked = $checked;
  }
  if (get_option('jcp_pp_filter_text_widget') != '') {
    $filter_text_widget_checked = $checked;
  }
  if (get_option('jcp_pp_include_css') != '') {
    $include_css_checked = $checked;
  }

?>
       <form method="post" action="options.php">
	 <?php wp_nonce_field('update-options') ?>
	 <label for="jcp_pp_filter_the_content">
	   Display progress meters in posts:
	   <input type="checkbox" id="jcp_pp_filter_the_content" 
		  name="jcp_pp_filter_the_content" <?php
      echo $filter_the_content_checked; 
		  ?> />
	 </label>
	 <br/>
	 <label for="jcp_pp_filter_text_widget">
	   Display progress meters in Text Widgets:
	   <input type="checkbox" id="jcp_pp_filter_text_widget" 
		  name="jcp_pp_filter_text_widget" <?php
      echo $filter_text_widget_checked; 
		  ?> />
	 </label>
	 <br/>
	 <label for="jcp_pp_include_css">
	   Use build-in styles:
	   <input type="checkbox" id="jcp_pp_include_css" 
		  name="jcp_pp_include_css" <?php
      echo $include_css_checked; 
		  ?> />
	 </label>
	 <input type="hidden" name="action" value="update" />
	 <input type="hidden" name="page_options" 
		value="jcp_pp_filter_the_content,jcp_pp_filter_text_widget,jcp_pp_include_css" />
	 <p class="submit">
	   <input type="submit" name="Submit" value="<?php _e('Update Options »') ?>" />
	 </p>
       </form>
     <?php
}

function jcp_pp_css_head() {
    $wp = get_bloginfo('wpurl');
    $css_url = $wp . '/wp-content/plugins/wp-progpress/wp-progpress.php?jcp_pp_action=css';
       print('<link rel="stylesheet" type="text/css" href="' . $css_url . '"/>');
}

       


if (!empty($_REQUEST['jcp_pp_action'])) {
  switch ($_REQUEST['jcp_pp_action']) {
       case 'css':
	 header("Content-type: text/css"); 
?>
div.jcp_pp { margin: 0 auto; padding: 0; text-align: center; width:200px; }
div.jcp_pp_title { font-weight: bold; }
div.jcp_pp_meter { overflow: hidden; width: 100%; height: 20px; border: 1px solid #000; padding: 2px; }   
div.jcp_pp_meter div {height: 100%; float: left; background-color: #000; }
<?php
         break;
       default:
         die();
   } 
} else {
    register_activation_hook(__FILE__,'jcp_progpress_set_options');
    register_deactivation_hook(__FILE__,'jcp_progpress_unset_options');
    add_action('admin_menu','jcp_progpress_modify_menu');
      
    if (get_option('jcp_pp_filter_the_content') != '') {
       add_filter('the_content','jcp_progpress_filter');
    }
       
    if (get_option('jcp_pp_filter_text_widget') != '') {
       add_filter('widget_text','jcp_progpress_filter');
    }

    if (get_option('jcp_pp_include_css') != '') {
      add_action('wp_head', 'jcp_pp_css_head');
    }
}