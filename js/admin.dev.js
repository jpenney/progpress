/*
  $Id$

  ProgPress

  Copyright 2010  Jason Penney (email: jpenney[at]jczorkmid.net)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

jQuery(function($) {

    function toggleStyles($link, handler) {
        var jcp_pp_area = $('#jcp_progpress_sample_output');
        if ($link.text().indexOf('Hide') !== 0) {
            $link.text($link.text().replace(/^(Show|Load) +/,'Hide '));
            jcp_pp_area.fadeIn("slow", handler);
        } else {
            $link.text($link.text().replace(/^Hide +/,'Show '));
            jcp_pp_area.fadeOut("slow", handler);
        }
    }

    function reformatStyle(css) {
        return css.replace(
                /[\n\s]+/g,' ').replace(
                        /(\})/g,'$1\n').replace(
                                /([{};])/g,'$1 ').replace(
                                        /(\{)/g, ' $1');
    }

    var loadStyles = function() {       
        var $this = $(this), 
            nullHandler = function() { },
            href, styleArea, pleaseWait;
        if ($this.data("loaded")) {
            toggleStyles($this, nullHandler);
        } else {
            pleaseWait = $('<span>' +
                           '<img style="vertical-align: middle" ' +
                           'src="images/wpspin_light.gif"/>' +
                          'Please wait...</span>');
            $this.fadeOut("fast",function() {
                $this.after(pleaseWait);
                styleArea = $('#jcp_progpress_styles');
                href = $this.attr('href');
                $.get(href, function(data) {
                    $('head').append(
                        $('<style type="text/css"/>').text(data));
                    styleArea.html('').append(
                        $('<pre style="overflow:auto;" />').text(
                            reformatStyle(data)));
                    $this.attr({ "target": '', "href": "#" });
                    $this.data("loaded",true);
                    toggleStyles($this,function() {                            
                        pleaseWait.fadeOut("fast", function() {
                            $this.fadeIn("fast");
                        });
                    });
                });
            });
        }
        return false;
    };

    $('#jcp_progpress_preview_styles').bind('click',loadStyles);
    $('#jcp_progpress_preview_container').show();

});

