
function isNumericWithCommas(str) {
    // 正则表达式，匹配仅包含数字和逗号的字符串
    var pattern = /^\d+(,\d+)*$/;
    return pattern.test(str);
}

function isDateString(dateString) {
    var date;
    // 尝试解析日期字符串
    date = new Date(dateString);
    // 检查返回的日期是否为NaN（不是一个数字），并且年份是否合法
    return !isNaN(date.getTime()) && date.getFullYear() <= 9999;
}


function validate()
{

    var programId   = $('#conditions').find('#programId').val();
    var projectId     = $('#conditions').find('#projectId').val();
    var projectEnd = $('#conditions').find('#projectEnd').val();
    var task_assignTo    = $('#conditions').find('#task_assignTo').val();
    var projectPM  = $('#conditions').find('#projectPM').val();
    var projectStatus   = $('#conditions').find('#projectStatus').val();
    var rowtype     = $('#conditions').find('#rowtype').val();
    var excutionId = $('#conditions').find('#excutionId').val();
    var storyId    = $('#conditions').find('#storyId').val();
    var task_finishedBy  = $('#conditions').find('#task_finishedBy').val();
    var task_estStarted  = $('#conditions').find('#task_estStarted').val();

    var params = {
        'programId' : $('#conditions').find('#programId').val(),
        'projectId' : $('#conditions').find('#projectId').val(),
        'projectEnd' : $('#conditions').find('#projectEnd').val(),
        'task_assignTo' : $('#conditions').find('#task_assignTo').val(),
        'projectPM' : $('#conditions').find('#projectPM').val(),
        'projectStatus' : $('#conditions').find('#projectStatus').val(),
        'rowtype' : $('#conditions').find('#rowtype').val(),
        'excutionId' : $('#conditions').find('#excutionId').val(),
        'storyId' : $('#conditions').find('#storyId').val(),
        'task_finishedBy' : $('#conditions').find('#task_finishedBy').val(),
        'task_estStarted'   : $('#conditions').find('#task_estStarted').val(),
    };

    var haserror = false;

    ['projectEnd','task_estStarted'].forEach(e => {
        var value = params[e];
        var b = value !== null && value !== undefined && value !== '';
        if (b){
            b = isDateString(value);
            // console.log(value+' isDateString:'+b);
            if(!b){
                // alert(value+' 不是日期格式(yyyy-mm-dd)的串!!!');
                var v = $('#conditions').find('#'+e+"Label");
                if( v == undefined || v.length == 0 ) {
                    $('#conditions').find('#'+e).parent().after('<div id="'+e+"Label"+'" class="text-danger  help-text" style="float:left;line-height:34px;">不是日期格式(yyyy-mm-dd)的串!!!</div>');
                }
                haserror = true;
            }
        };
    });   

    ['programId','projectId','excutionId','storyId'].forEach(e => {

        var value = params[e];
        var b = value !== null && value !== undefined && value !== '';
        if (b){
            b = isNumericWithCommas(value);
            // console.log(value+' isNumericWithCommas:'+b);
            if(!b){
                // alert(value+' 不是数字格式(数字[,数字])的串!!!' );
                var v = $('#conditions').find('#'+e+"Label");
                if( v == undefined || v.length == 0 ) {
                    $('#conditions').find('#'+e).parent().after('<div id="'+e+"Label"+'" class="text-danger  help-text" style="float:left;line-height:34px;">不是数字格式(数字[,数字])的串!!!</div>');
                }
                haserror = true;
            }
        }
    });   

    return !haserror;
}

$('.form-control').on('focus', function() {
    $('#'+$(this).attr('id')+'Label').remove();
});

function query()
{

    var programId   = $('#conditions').find('#programId').val();
    var projectId     = $('#conditions').find('#projectId').val();
    var projectEnd = $('#conditions').find('#projectEnd').val();
    var task_assignTo    = $('#conditions').find('#task_assignTo').val();
    var projectPM  = $('#conditions').find('#projectPM').val();
    var projectStatus   = $('#conditions').find('#projectStatus').val();
    var rowtype     = $('#conditions').find('#rowtype').val();
    var excutionId = $('#conditions').find('#excutionId').val();
    var storyId    = $('#conditions').find('#storyId').val();
    var task_finishedBy  = $('#conditions').find('#task_finishedBy').val();
    var task_estStarted  = $('#conditions').find('#task_estStarted').val();

    var params = {
        'programId' : $('#conditions').find('#programId').val(),
        'projectId' : $('#conditions').find('#projectId').val(),
        'projectEnd' : $('#conditions').find('#projectEnd').val(),
        'task_assignTo' : $('#conditions').find('#task_assignTo').val(),
        'projectPM' : $('#conditions').find('#projectPM').val(),
        'projectStatus' : $('#conditions').find('#projectStatus').val(),
        'rowtype' : $('#conditions').find('#rowtype').val(),
        'excutionId' : $('#conditions').find('#excutionId').val(),
        'storyId' : $('#conditions').find('#storyId').val(),
        'task_finishedBy' : $('#conditions').find('#task_finishedBy').val(),
        'task_estStarted'   : $('#conditions').find('#task_estStarted').val(),
    };
    
    var keyValuePairs = Object.keys(params)
        .map(function(key) {
            var value = params[key];
            // 如果值存在且不为空字符串，则拼接为 k=v 格式，否则忽略
            // return value !== null && value !== undefined && value !== '' ? key + '=' + encodeURIComponent(value) : null;
            return value !== null && value !== undefined && value !== '' ? key + '=' + value : key + '=';
        })
        // 过滤掉null值，确保只有有效的k=v对被包含
        .filter(Boolean);
    
    var queryString = keyValuePairs.join('&');
    // console.log(queryString);

    var link = createLink('gantt', 'frappe', queryString);
    console.log(queryString+' => '+link);
    location.href = link;
}

function reset()
{
    $('#conditions').find('#programId').val('223');
    $('#conditions').find('#projectId').val('');
    $('#conditions').find('#projectEnd').val('');
    $('#conditions').find('#task_assignTo').val('').trigger('chosen:updated');
    $('#conditions').find('#projectPM').val('').trigger('chosen:updated');
    $('#conditions').find('#projectStatus').val('').trigger('chosen:updated');
    $('#conditions').find('#rowtype').val('').trigger('chosen:updated');
    $('#conditions').find('#excutionId').val('');
    $('#conditions').find('#storyId').val('');
    $('#conditions').find('#task_finishedBy').val('');
    $('#conditions').find('#task_estStarted').val('');


    // if(!$searchForm.find('#value' + i).hasClass('picker-select')) $searchForm.find('#value' + i).val('').trigger('chosen:updated');
    // if($searchForm.find('#value' + i).hasClass('picker-select'))  $searchForm.find('#value' + i).data('zui.picker').setValue('');
    // $searchForm.find('#value' + i + '.date').val('').attr('placeholder', '');
    

    // var link = createLink('gantt', 'frappe', "programId=223&projectStatus=wait,doing");
    // location.href = link;

}

/**
 * Convert a date string to date object in js.
 *
 * @param  string $date
 * @access public
 * @return date
 */
function convertStringToDate(dateString)
{
    dateString = dateString.split('-');
    return new Date(dateString[0], dateString[1] - 1, dateString[2]);
}

/**
 * Compute the diff days of two date.
 *
 * @param  string $date1
 * @param  string $date1
 * @access public
 * @return int
 */
function diffDate(date1, date2)
{
    date1 = convertStringToDate(date1);
    date2 = convertStringToDate(date2);
    delta = (date2 - date1) / (1000 * 60 * 60 * 24) + 1;

    weekEnds = 0;
    for(i = 0; i < delta; i++)
    {
        if((weekend == 2 && date1.getDay() == 6) || date1.getDay() == 0) weekEnds ++;
        date1 = date1.valueOf();
        date1 += 1000 * 60 * 60 * 24;
        date1 = new Date(date1);
    }
    return delta - weekEnds;
}

$(function()
{
    var options =
    {
        language: config.clientLang,
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1,
        minView: 2,
        format: 'yyyy-mm-dd',
        startDate: new Date()
    };
    $('input#begin,input#end').fixedDate().datetimepicker(options);
});
