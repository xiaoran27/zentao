
<?php 
if( $type == 'requirement' || $story->type == 'requirement' ){
  $config->story->create->requiredFields .= ",purchaser,bzCategory"; 
  $config->story->edit->requiredFields .= ",purchaser,bzCategory"; 
  $config->story->change->requiredFields .= ",purchaser,bzCategory"; 
}

// $common->syncStarlink();

$bizProjects = $this->loadModel('project')->getPairsListForB100();
$bizProjectList = $bizProjects ;
js::set('bizProjects', $bizProjects);
js::set('bizProjectList', $bizProjectList);

// echo $bizProjectList;
?>

<?php include '../../../../../module/common/view/header.html.php';?>
<?php include '../../../../../module/common/view/kindeditor.html.php';?>
<?php js::set('rawMethod', $this->app->rawMethod);?>
<script>

/**
 * 选择客户后同步赋值 客户分层/行为分.
 *
 * @param  int   $purchaserValue
 * @access public
 * @return void
 */
function set_bzCategory_scoreNum(purchaserValue)
{
    _purchaserValue = $('#purchaser').val();
    if ( ! _purchaserValue ) return;
    // selectedCode = $("#purchaser").next().find('div.picker-selections > div:last').attr("title");  //最后选择的客户名称
    selectedCode = $("#purchaser").next().find('div.picker-selections > div:first').attr("title");  //第一个选择的客户名称

    // console.log("当前传递客户="+purchaserValue+"; 全部客户="+list+"; 最后一个客户="+selectedCode);

    $.get(createLink('story', 'getPurchaserList', "codeOrName="+selectedCode, "json"), function(data)
    {
        if(data)
        {  
            data = eval("(" + data + ")");  //code,name,category,scoreNum
            // console.log("get返回的客户="+JSON.stringify(data));
            value = data[selectedCode];
            valueList = value.split(",");
            

            if (valueList.length > 1 ) {
                // $("#bzCategory_chosen > a > span").html(valueList[2]);
                // $("#bzCategory_chosen > a > div.chosen-search > input[type=text]").attr('placeholder',valueList[2]);
                $("#bzCategory").find("option[value='"+valueList[2]+"']").attr("selected",true);
            }
            if (valueList.length>2) $('#scoreNum').val(valueList[3]);
        }
    })

}


/**
 * Load branch.
 *
 * @access public
 * @return void
 */
function loadBranch()
{
    var branch    = $('#branch').val();
    var productID = $('#product').val();
    if(typeof(branch) == 'undefined') branch = 0;
    if(typeof(productID) == 'undefined' && config.currentMethod == 'edit') productID = oldProductID;

    loadProductModules(productID, branch);
    loadProductPlans(productID, branch);
}

/**
 * Load branches when change product.
 *
 * @param  int   $productID
 * @access public
 * @return void
 */
function loadProductBranches(productID)
{
    var param = 'all';
    if(page == 'create') param = 'active';
    $('#branch').remove();
    $('#branch_chosen').remove();
    $.get(createLink('branch', 'ajaxGetBranches', "productID=" + productID + "&oldBranch=0&param=" + param + "&projectID=" + executionID), function(data)
    {
        var $product = $('#product');
        var $inputGroup = $product.closest('.input-group');
        $inputGroup.find('.input-group-addon').toggleClass('hidden', !data);
        if(data)
        {
            $inputGroup.append(data);
            $('#branch').css('width', config.currentMethod == 'create' ? '120px' : '65px').chosen();
        }
        $inputGroup.fixInputGroup();

        loadProductModules(productID, $('#branch').val());
        loadProductPlans(productID, $('#branch').val());
    })
}

/**
 * Load modules when change product.
 *
 * @param  int    $productID
 * @param  int    $branch
 * @access public
 * @return void
 */
function loadProductModules(productID, branch)
{
    if(typeof(branch) == 'undefined') branch = $('#branch').val();
    if(!branch) branch = 0;

    var currentModule = 0;
    if(rawMethod == 'edit')
    {
        currentModule = $('#module').val();
    }

    var moduleLink = createLink('tree', 'ajaxGetOptionMenu', 'productID=' + productID + '&viewtype=story&branch=' + branch + '&rootModuleID=0&returnType=html&fieldID=&needManage=true&extra=&currentModuleID=' + currentModule);
    var $moduleIDBox = $('#moduleIdBox');
    $moduleIDBox.load(moduleLink, function()
    {
        $moduleIDBox.find('#module').chosen();
        if(typeof(storyModule) == 'string' && config.currentMethod != 'edit') $moduleIDBox.prepend("<span class='input-group-addon'>" + storyModule + "</span>");
        $moduleIDBox.fixInputGroup();
    });
}

/**
 * Load plans when change product.
 *
 * @param  int    $productID
 * @param  int    $branch
 * @access public
 * @return void
 */
function loadProductPlans(productID, branch)
{
    if(typeof(branch) == 'undefined') branch = 0;
    if(!branch) branch = 0;
    var expired = config.currentMethod == 'create' ? 'unexpired' : '';
    planLink = createLink('product', 'ajaxGetPlans', 'productID=' + productID + '&branch=' + branch + '&planID=' + $('#plan').val() + '&fieldID=&needCreate=true&expired='+ expired +'&param=skipParent,' + config.currentMethod);
    var $planIdBox = $('#planIdBox');
    $planIdBox.load(planLink, function()
    {
        $planIdBox.find('#plan').chosen();
        $planIdBox.fixInputGroup();
    });
}

/**
 * Load reviewers when change product.
 *
 * @param  int    $productID
 * @access public
 * @return void
 */
function loadProductReviewers(productID)
{
    var storyID       = <?php echo isset($story->id) ? $story->id : 0;?>;
    var reviewerLink  = createLink('product', 'ajaxGetReviewers', 'productID=' + productID + '&storyID=' + storyID);
    var needNotReview = $('#needNotReview').attr('checked');
    $.get(reviewerLink, function(data)
    {
        if(data)
        {
            var $reviewer = $('#reviewer');
            var chosen = $reviewer.data('chosen');
            if(chosen)
            {
                chosen.destroy();
            }
            else
            {
                var picker = $reviewer.data('zui.picker');
                if(picker) picker.destroy();
            }
            $reviewer.replaceWith(data);
            $reviewer = $('#reviewer');
            if($reviewer.data('pickertype')) $reviewer.picker({chosenMode: true});
            else $reviewer.chosen();
            if(needNotReview == 'checked') $('#reviewer').attr('disabled', 'disabled').trigger('chosen:updated');
        }
    });
}
</script>
