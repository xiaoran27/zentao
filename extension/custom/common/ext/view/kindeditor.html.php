<?php if($extView = $this->getExtViewFile(__FILE__)){include $extView; return helper::cd();}?>
<?php
$module = $this->moduleName;
$method = $this->methodName;
if(!isset($config->$module->editor->$method)) return;
$editor = $config->$module->editor->$method;
$editor['id'] = explode(',', $editor['id']);
$editorLangs  = array('en' => 'en', 'zh-cn' => 'zh_CN', 'zh-tw' => 'zh_TW', 'ja' => 'ja');
$editorLang   = isset($editorLangs[$app->getClientLang()]) ? $editorLangs[$app->getClientLang()] : 'en';

/* set uid for upload. */
$uid = uniqid('');
?>
<?php js::import($jsRoot . 'kindeditor/kindeditor.min.js'); ?>
<script src='<?php echo $jsRoot;?>kindeditor/plugins/holder/holder.js'></script>
<?php js::import($jsRoot . "kindeditor/lang/{$editorLang}.js");?>
<script>
(function($) {
    var kuid = '<?php echo $uid;?>';
    var editor = <?php echo json_encode($editor);?>;
    var K = KindEditor;

    var measurementTools =
    [ 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic','underline', '|',
    'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|',
    'emoticons', 'image', 'code', 'link', 'table', '|', 'removeformat','undo', 'redo', 'fullscreen', 'source', 'about', 'holder'];
    var bugTools =
    [ 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic','underline', '|',
    'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|',
    'emoticons', 'image', 'code', 'link', '|', 'removeformat','undo', 'redo', 'fullscreen', 'source', 'about'];
    var simpleTools =
    [ 'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic','underline', '|',
    'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist', 'insertunorderedlist', '|',
    'emoticons', 'image', 'code', 'link', 'table', '|', 'removeformat','undo', 'redo', 'fullscreen', 'source', 'about'];
    var fullTools =
    [ 'formatblock', 'fontname', 'fontsize', 'lineheight', '|', 'forecolor', 'hilitecolor', '|', 'bold', 'italic','underline', 'strikethrough', '|',
    'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|',
    'insertorderedlist', 'insertunorderedlist', '|',
    'emoticons', 'image', 'insertfile', 'hr', '|', 'link', 'unlink', '/',
    'undo', 'redo', '|', 'selectall', 'cut', 'copy', 'paste', '|', 'plainpaste', 'wordpaste', '|', 'removeformat', 'clearhtml','quickformat', '|',
    'indent', 'outdent', 'subscript', 'superscript', '|',
    'table', 'code', '|', 'pagebreak', 'anchor', '|',
    'fullscreen', 'source', 'preview', 'about'];
    var docTools =
    [ 'formatblock', 'fontname', 'fontsize', 'lineheight', '|', 'forecolor', 'hilitecolor', '|', 'bold', 'italic','underline', 'strikethrough', '|',
    'justifyleft', 'justifycenter', 'justifyright', 'justifyfull', '|',
    'insertorderedlist', 'insertunorderedlist', '|',
    'emoticons', 'image', 'hr', '|', 'link', '|',
    'undo', 'redo', '|', 'selectall', 'cut', 'copy', 'paste', '|', 'plainpaste', 'wordpaste', '|', 'removeformat', 'clearhtml','quickformat', '|',
    'indent', 'outdent', 'subscript', 'superscript', '|',
    'table', 'code', 'pagebreak',
    'source'];
    var editorToolsMap = {fullTools: fullTools, simpleTools: simpleTools, bugTools: bugTools, measurementTools: measurementTools, docTools: docTools};

    // Kindeditor default options
    var editorDefaults =
    {
        cssPath: [config.themeRoot + 'zui/css/min.css'],
        width: '100%',
        height: '200px',
        filterMode: true,
        bodyClass: 'article-content',
        urlType: 'absolute',
        uploadJson: createLink('file', 'ajaxUpload', 'uid=' + kuid),
        allowFileManager: true,
        langType: '<?php echo $editorLang?>',
        cssData: 'html,body {background: none}.article-content{overflow:visible}.article-content, .article-content table td, .article-content table th {line-height: 1.3846153846; font-size: 13px;}.article-content .table-auto {width: auto!important; max-width: 100%;}',
        placeholderStyle: {fontSize: '13px', color: '#888'},
        pasteImage: {postUrl: createLink('file', 'ajaxPasteImg', 'uid=' + kuid), placeholder: <?php echo json_encode($lang->noticePasteImg);?>},
        syncAfterBlur: true,
        spellcheck: false
    };

    window.editor = {};

    // Init kindeditor
    var setKindeditor = function(element, options)
    {
        var $editor  = $(element);
        var pasted   = false;
        var editorID = $editor.attr('id');
        // var editorID = ($editor.attr('id')+"___"+$editor.attr('name')).replace(/[\[\]]/g, "_");
        options      = $.extend({}, editorDefaults, $editor.data(), options);
        if(editorID === undefined)
        {
            editorID = 'kindeditor-' + $.zui.uuid();
            $editor.attr('id', editorID);
        }
        // console.log("editorID="+editorID+"; options="+JSON.stringify(options));
        var editorTool  = editorToolsMap[options.tools || editor.tools] || simpleTools;

        /* Remove fullscreen in modal. */
        if(config.onlybody == 'yes')
        {
            var newEditorTool = new Array();
            for(i in editorTool)
            {
                if(editorTool[i] != 'fullscreen') newEditorTool.push(editorTool[i]);
            }
            editorTool = newEditorTool;
        }

        $.extend(options,
        {
            holderEditText: '<?php echo $this->lang->edit?>', // 设置 holder 鼠标悬停时的提示编辑文本
            holderEdit: true, // 启用编辑 holder 功能
            items: editorTool,
            placeholder: $editor.attr('placeholder') || options.placeholder || '',
            pasteImage: {postUrl: createLink('file', 'ajaxPasteImg', 'uid=' + kuid), placeholder: $editor.attr('placeholder') || <?php echo json_encode($lang->noticePasteImg);?>},
        });

        try
        {
            var keditor = K.create('#' + editorID, options);
            window.editor['#'] = window.editor[editorID] = keditor;
            $editor.data('keditor', keditor);
            return keditor;
        }
        catch(e){
            console.log("setKindeditor: e="+JSON.stringify(e));
            return false;}
    };

    // Init kindeditor with jquery way
    $.fn.kindeditor = function(options)
    {
        return this.each(function()
        {
            setKindeditor(this, options);
        });
    };

    // Init all kindeditor
    var initKindeditor = function(afterInit)
    {
        var $submitBtn = $('form :input[type=submit]');
        if($submitBtn.length)
        {
            $submitBtn.next('#uid').remove();
            $submitBtn.after("<input type='hidden' id='uid' name='uid' value=" + kuid + ">");
        }
        if($.isFunction(afterInit)) afterInit();
        $.each(editor.id, function(key, editorID)
        {
            // setKindeditor('#' + editorID);

            // textarea同ID的多元素的处理
            var i=0;
            $('textarea[id^="'+editorID+'"]').each(function() {

                var oldId = this.id;
                var changedId = this.id == editorID || this.id == editorID+'[]';
                if (changedId) this.id = editorID+i;  //设置不同的ID值给KE用
                setKindeditor('#' + this.id);
                if (changedId) this.id = oldId;  //切回原来的ID值
                i=i+1;

                console.log('changedId='+changedId+'; id='+this.id);
            })
            
        });
    };

    // Init all kindeditors when document is ready
    $(initKindeditor);

}(jQuery));

</script>
