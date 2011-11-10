<?php
/*
Plugin Name: ProgPress - NaNoWriMo Support
Plugin URI: http://jasonpenney.net/wordpress-plugins/progpress/
Description: Adds support for NaNoWriMo API to ProgPress ([progpress nanowrimo=*username* ])
Version: 1.2
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

/**
 * NaNoWriMo API support plugin for ProgPress
 *
 * @package ProgPress
 * @subpackage NaNoWriMo
 * @since 1.1
 * @version 1.1
 */



/** 
 * This file holds the actual guts of the plugin
 */
require_once(dirname(__FILE__) . '/php/class.JCP_ProgPress_NaNoWriMo.php');
if (class_exists("JCP_ProgPress_NaNoWriMo")) {
  register_activation_hook(__FILE__,
                           array('JCP_ProgPress_NaNoWriMo',
                                 'activate_plugin'));
  add_action('init',array('JCP_ProgPress_NaNoWriMo','init_plugin'));
}
