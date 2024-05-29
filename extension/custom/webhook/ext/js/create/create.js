$(function()
{
    $('#type').change(function()
    {
        var type = $(this).val();
        $('#sendTypeTR').toggle(type != 'dinggroup' && type != 'dinggroupapi' && type != 'dinguser' && type != 'dingsingleuser' && type != 'wechatuser' && type != 'wechatgroup' && type != 'feishuuser' && type != 'feishugroup');
        $('#secretTR').toggle(type == 'dinggroup' || type == 'feishugroup');
        $('#urlTR').toggle(type != 'dinguser' && type != 'dingsingleuser' && type != 'dinggroupapi' && type != 'wechatuser' && type != 'feishuuser');
        $('.dinguserTR').toggle(type == 'dinguser' || type == 'dingsingleuser'  || type == 'dinggroupapi' );
        $('#openConversationIdTR').toggle(type == 'dinggroupapi' );
        $('.wechatTR').toggle(type == 'wechatuser');
        $('.feishuTR').toggle(type == 'feishuuser');
        $('#paramsTR').toggle(type != 'bearychat' && type != 'dinggroup' && type != 'dinggroupapi' && type != 'dinguser' && type != 'dingsingleuser' && type != 'wechatuser' && type != 'wechatgroup' && type != 'feishuuser' && type != 'feishugroup');
        $('#urlNote').html(urlNote[type]);
    });

    $('.objectType').click(function()
    {
        if($(this).prop('checked'))
        {
            $(this).parent().parent().next().find('input[type=checkbox]').attr('checked', 'checked');
        }
        else
        {
            $(this).parent().parent().next().find('input[type=checkbox]').removeAttr('checked');
        }
    });

    $('#allParams, #allActions').click(function()
    {
        if($(this).prop('checked'))
        {
            $(this).parents('tr').find('input[type=checkbox]').attr('checked', 'checked');
        }
        else
        {
            $(this).parents('tr').find('input[type=checkbox][disabled!=disabled]').removeAttr('checked');
        }
    });

    $('#name').focus();
    $('#type').change();
    $('#paramstext').attr('disabled', 'disabled');
});
