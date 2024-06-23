$(function()
{

    $('#showGantt, #showTable').click(function() {
        if ($(this).hasClass('btn-active-text')) return false;

        $(this).addClass('btn-active-text')
        if ($(this).attr('id') === 'showGantt') {
            $('#showTable').removeClass('btn-active-text');
            $('#dataContent').hide();
            $('#ganttContent').show();
        } else if ($(this).attr('id') === 'showTable') {
            $('#showGantt').removeClass('btn-active-text');
            $('#ganttContent').hide();
            $('#dataContent').show();
        }
    });
        

    if($('#taskList thead th.c-name').width() < 350) $('#taskList thead th.c-name').width(350);
    $('#taskList td.has-child .task-toggle').each(function() {
        var $td = $(this).closest('td');
        var labelWidth = 0;
        if($td.find('.label').length > 0) labelWidth = $td.find('.label').width();
        $td.find('a').eq(0).css('max-width', $td.width() - labelWidth - 60);
    });

    toggleFold();


    /**
     * Toggle fold for parent.
     *
     * @access public
     * @return void
     */
    function toggleFold()
    {
        $table     = $('#taskList');
        $parentTd = $table.find('td.has-child');
        if($parentTd.length == 0) return false;

        var toggleClass = 'task-toggle';
        var nameClass   = 'c-name';
        $table.find('th.' + nameClass).addClass('clearfix').append("<span id='toggleFold'><i  class='icon icon-angle-double-right'></i></span>");
        // $('#toggleFold').addClass('collapsed');
        $parentTd.each(function()
        {
            var dataID = $(this).closest('tr').attr('data-id');
            $table.find('tr.parent-' + dataID).hide();
            // $(this).find('a.' + toggleClass).addClass('collapsed');
            // $(this).find('a.' + toggleClass).toggleClass('collapsed', true);
        })
        // $table.find('th.' + nameClass + ' #toggleFold').toggleClass('collapsed', true);

        $(document).on('click', '#toggleFold', function()
        {
            var collapsed   = $(this).hasClass('collapsed');
            $parentTd.each(function()
            {
                var dataID = $(this).closest('tr').attr('data-id');
                $table.find('tr.parent-' + dataID).toggle(!collapsed);
                $table.find('a.' + toggleClass).toggleClass('collapsed', !collapsed);
            })

            $(this).toggleClass('collapsed', !collapsed);
        });

        $parentTd.find('a.' + toggleClass).click(function()
        {
            var collapsed   = $(this).hasClass('collapsed');
            var dataID      = $(this).closest('tr').attr('data-id');
            $trParents = $table.find('tr.parent-' + dataID);
            $trParents.toggle(!collapsed);
            $(this).toggleClass('collapsed', !collapsed);

            if($trParents.length > 0) {
                $trParents.each(function()
                {
                    var dataID2 = $(this).attr('data-id');
                    $trParents2 = $table.find('tr.parent-' + dataID2);
                    $trParents2.toggle(!collapsed);
                    $(this).toggleClass('collapsed', !collapsed);

                    if($trParents2.length > 0) {
                        $trParents2.each(function()
                        {
                            var dataID3 = $(this).attr('data-id');
                            $trParents3 = $table.find('tr.parent-' + dataID3);
                            $trParents3.toggle(!collapsed);
                            $(this).toggleClass('collapsed', !collapsed);
                        })
                    }
                })
            }

            // $table = $(this).closest('table');
            setTimeout(function()
            {
                hasCollapsed = $table.find('td.has-child a.' + toggleClass + '.collapsed').length != 0;
                $('#toggleFold').toggleClass('collapsed', hasCollapsed);
            }, 100);

        });
    }

    // $('#toggleFold').trigger('click');

});