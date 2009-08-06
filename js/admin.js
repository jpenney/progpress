jQuery(function($) {

    function toggleStyles($link) {
        var cell =  $($link.parents('tr')[0]).find('td');
        var row2 =  $('tr#jcp_progpress_sample_output');
        if ($link.text().indexOf('View') == 0) {
            $link.text($link.text().replace(/^View +/,''));
            cell.fadeIn("slow");
            row2.fadeIn("slow");
        } else {
            $link.text('View ' + $link.text());
            cell.fadeOut("slow");
            row2.fadeOut("slow");
        }
    }

        
    var loadStyles = function() {       
        var $this =$(this);
        if ($this.data("loaded")) {
            toggleStyles($this);
        } else {
            var href = $this.attr('href');
            $.get(href, function(data) {
                var previewRow = $($this.parents('tr')[0]);
                var previewCell =  $('td', previewRow);
                previewCell.append($('<span>Please wait...</span>'));
                previewCell.fadeOut("slow", function() {
                    previewCell
                        .html($('<pre style="overflow:auto;" />')
                              .text(data));
                    $this.attr('href','#')
                        .attr('target','');
                    $this.data("loaded",true);
                    toggleStyles($this);
                });
                $('head').append($('<style type="text/css"/>').text(data));
            });
        }
        return false;
    };

    $('#jcp_progpress_preview_styles').bind('click',loadStyles);
});