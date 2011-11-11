<?php
/*

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
 * @since 1.1
 */

if (!class_exists("JCP_ProgPress_NaNoWriMo")) {
  /**
   * JCP_ProgPress_NaNoWriMo 
   * 
   * Adds new 'nanowrimo' attribute to [progpress] shortcode, and interfaces 
   * with NaNoWriMo API
   *
   * @package ProgPress
   * @since 1.1
   * @link http://www.nanowrimo.org/wordcount_api  NaNoWriMo API
   */
  class JCP_ProgPress_NaNoWriMo {

    function JCP_ProgPress_NaNoWriMo(){$this->__construct();}
    
    function __construct(){
      $this->_currentTag = null;
      $this->_result = array();
      $this->_parser = null;
      $this->_api_query_url = 
        'http://www.nanowrimo.org/wordcount_api/wchistory/%1$s';
    }

    /* XML Parser */
    
    /**
     * Parses start tags
     * 
     * @param object $parser 
     * @param string $tagName 
     * @param array $attrs 
     * @access protected
     * @return void
     */
    protected function _xml_start_element($parser, $tagName, $attrs) {
      $this->_currentTag = trim($tagName);
    }

    /**
     * Parses tag content
     * 
     * @param object $parser 
     * @param string $data 
     * @access protected
     * @return void
     */
    protected function _xml_contents($parser, $data) {
      $tmp = trim($data);
      if ($this->_currentTag && $tmp) {
        if (array_key_exists($this->_currentTag,$this->_result)) {
          $this->_result['_PREVIOUS_' . $this->_currentTag] =
            $this->_result[$this->_currentTag];
        }
        $this->_result[$this->_currentTag] = $tmp;
      }
    }

    /**
     * Parses end tags
     * 
     * @param object $parser 
     * @param string $tagName 
     * @access protected
     * @return void
     */
    protected function _xml_end_element($parser, $tagName) {
      $this->_currentTag = null;
    }

    /**
     * Parse XML payload
     *
     * Parses the XML payload returned from the 'user wordcount history' 
     * NaNoWriMo Word Count API
     * 
     * @param string $data xml payload
     * @access public
     * @return array containing (partial) results for NaNoWrimo Work Count 
     * API call 
     */
    public function parse($data) {
      $this->_result = array();
      $this->_parser = xml_parser_create();
      xml_set_element_handler($this->_parser, array($this,'_xml_start_element'), 
                              array($this,'_xml_end_element'));
      xml_set_character_data_handler($this->_parser, array($this,'_xml_contents'));
      xml_parse($this->_parser, $data, true);
      xml_parser_free($this->_parser);
      $this->_parser = null;
      return $this->_result;
    }

    /* Data Handling */

    /**
     * Retrieve NaNoWriMo 'user wordcount history' 
     * 
     * Queries NaNoWriMo word count API for 'user wordcount history'.
     * Data is stored as a transient for up to two hours.  It is also
     * cached in the database as a fall back in case the API is down.
     *
     * @param str $username NaNoWriMo User Name.
     * @access public
     * @return array containing (partial) results for NaNoWrimo Work Count 
     * API call.  Any errors encountered will be stored in $result['ERROR'].
     */
    public function get_history($username) {
      if (is_numeric($username)) {
        $result = array();
        $result['ERROR'] = "Please switch to using your username";
        return $result;
      }
      $trans_id = "jcp_pp_nanowrimo_$username";
      $cached = true;
      // check for transient data first to prevent
      // hammering on server
      if (false === ( $data = get_transient($trans_id) )) {
        // no data in transient, let's try again
        $cached = false;
        $query_url = sprintf($this->_api_query_url, $username);
        $data =  wp_remote_retrieve_body( wp_remote_get( $query_url ) );
        // set initial transient with short timeout
        // in case it contains an error
        set_transient( $trans_id, $data, 120 );
      }
      $result = null;
      if ($data != null) {
        $result = $this->parse($data);        
      }
      if ($result != null && is_array($result)) {
        if (!$cached && array_key_exists('USER_WORDCOUNT',$result)) {
          // fresh data
          // update transient with longer timout
          set_transient( $trans_id, $data, 60 * 60 * 2 );
          // store in options table in case site is down later
          $cache = get_option('jcp_progpress_nanowrimo');
          $cache[$username] = $result;
          update_option('jcp_progpress_nanowrimo', $cache);
        }
      } else {
        // there was an error getting new data
        // load cached data if it exists
        $cache = get_option('jcp_progpress_nanowrimo');
        if (array_key_exists( $username, $cache ) ) {
          $result = $cache[$username];
        }
      }
      return $result;
    }
  

    /**
     * Plugin activation
     * 
     * Prepares options.
     *
     * @static
     * @access public
     * @return void
     */
    static function activate_plugin() {
      add_option('jcp_progpress_nanowrimo', 
                 array('_version' => JCP_PP_VERSION));
    }

    /**
     * Hooks into jcp_progpress_shortcode_atts.
     *
     * Parses out the 'nanowrimo' attribute from the 'progress'
     * shortcode.  If found, the shortcode executes in NaNoWriMo mode,
     * otherwise has no effect.
     * 
     * @param array $opts Contains arguments to pass to 
     * {@link jcp_progpress_generate_meter}, parsed out of {@link $atts}.
     * @param array $atts User defined attributes in shortcode tag.
     * @static
     * @access public
     * @return Combined and filtered attribute list.
     * @see shortcode_atts
     */
    static function shortcode_atts_filter($opts,$atts) {
      $opts = array_merge($opts, 
                          shortcode_atts(array('nanowrimo'=>''),
                                       $atts));
      if ($opts['nanowrimo'] !== '') {
        $NaNoWriMo = new JCP_ProgPress_NaNoWriMo();
        $nano = $NaNoWriMo->get_history($opts['nanowrimo']);
        $opts['goal'] = 50000;
        $opts['current'] = 0;
        $opts['previous'] = 0;
        $opts['class'] .= ' jcp_pp_nanowrimo ';
        if (array_key_exists('USER_WORDCOUNT',$nano)) {
          $opts['current'] = $nano['USER_WORDCOUNT'];
          if(array_key_exists('_PREVIOUS_WC',$nano)) {
            $opts['previous'] = $nano['_PREVIOUS_WC'];
          }
        } 
        if (array_key_exists('ERROR', $nano)) {
          $opts['error'] = '<b>NaNoWriMo Wodcount API Error ' .
            '(username=' .
            $opts['nanowrimo'] .")</b> " . $nano['ERROR'];
        }
      }
      return $opts;
    }
    
   /**
    * Initialize plugin
    * 
    * @static
    * @access public
    * @return void
    */
   static function init_plugin() {
      if (!is_admin()) {
        add_filter('jcp_progpress_shortcode_atts', 
                   array('JCP_ProgPress_NaNoWriMo', 'shortcode_atts_filter'),
                   10, 2);
      }
    }
  }
}
