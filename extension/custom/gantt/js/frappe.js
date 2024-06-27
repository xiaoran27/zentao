
function tableToExcel(ele) {
    const _ele = ( typeof ele === 'string' ) ? document.querySelector("#"+ele): ele;
    $ele = $(_ele);
    var tableName = $ele.attr('id')+"_"+Date.now();
    $ele.table2excel({
        exclude : ".noExl", //过滤位置的 css 类名
        filename : tableName+".xls", //文件名称
        name: "Excel Document Name.xlsx",
        exclude_img: false,//是否导出图片 false导出
        exclude_links: false,//是否导出链接 false导出
        exclude_inputs: true//是否导出输入框的值 true导出
    });
}
function exportGantt() 
{
    if ($('#showGantt').hasClass('btn-active-text')) html2img("gantt");
    if ($('#showData').hasClass('btn-active-text')) tableToExcel('taskList');
    if ($('#showStat').hasClass('btn-active-text')) html2img("statContent");
}

$(function()
{
    
    $('#showGantt, #showData, #showStat').click(function() {
        if ($(this).hasClass('btn-active-text')) return false;

        var id = $(this).attr('id').replace('show','');
        var cont = 'Content';
        ['Gantt','Data','Stat'].forEach(e => {
            if (e !== id ) {
                $('#show'+e).removeClass('btn-active-text');
                $('#'+e.toLowerCase()+cont).hide();
            }else{
                $(this).addClass('btn-active-text');
                $('#'+e.toLowerCase()+cont).show();
            }
        });
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
        $parentTds = $table.find('td.has-child');
        if($parentTds.length == 0) return false;

        var toggleClass = 'task-toggle';
        var nameClass   = 'c-name';
        $table.find('th.' + nameClass).addClass('clearfix').append("<span id='toggleFold' ><i  class='icon icon-angle-double-right'></i></span>");
        $('#toggleFold').addClass('collapsed');
        $parentTds.each(function()
        {
            var dataID = $(this).closest('tr').attr('data-id');
            $table.find('tr.parent-' + dataID).hide();
            $(this).find('a.' + toggleClass).addClass('collapsed');
            // $(this).find('a.' + toggleClass).toggleClass('collapsed', true);
        })
        // $table.find('th.' + nameClass + ' #toggleFold').toggleClass('collapsed', true);
        

        $('#toggleFold').on('click', function()
        {
            var collapsed   = $(this).hasClass('collapsed');
            $parentTds.each(function()
            {
                var dataID = $(this).closest('tr').attr('data-id');
                $table.find('tr.parent-' + dataID).toggle(!collapsed);
                $table.find('a.' + toggleClass).toggleClass('collapsed', !collapsed);
            })

            $(this).toggleClass('collapsed', !collapsed);
        });

        $parentTds.find('a.' + toggleClass).click(function()
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

    $('#toggleFold').trigger('click');

});