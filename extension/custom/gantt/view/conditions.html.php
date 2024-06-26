<div id='mainContent' class='main-row'>
<div class='main-col' id='conditions'>
    <div class='cell'>
      <div class="row">
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->programId;?></span>
            <?php echo html::select('programId', array(''=>'')+$programPairs, $programId, "class='form-control chosen searchSelect' ");?>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->projectId;?></span>
            <?php echo html::select('projectId', array(''=>'')+$projectPairs, $projectId, "class='form-control picker-select searchSelect' multiple ");?>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->projectEnd;?></span>
            <div class='datepicker-wrapper datepicker-date'><?php echo html::input('projectEnd', $projectEnd, "class='form-control form-date' ");?></div>
            </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->projectPM;?></span>
            <?php echo html::select('projectPM', $users, $projectPM, "class='form-control picker-select searchSelect' multiple");?>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->projectStatus;?></span>
            <?php echo html::select('projectStatus', $lang->gantt->projectStatusList, empty($projectStatus)?'unclosed':$projectStatus, "class='form-control picker-select' multiple");?>
          </div>
        </div>
      </div>
      <div style='height: 2px' ></div>
      <div class="row">
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->rowtype;?></span>
            <?php echo html::select('rowtype', $lang->gantt->typeList, $rowtype, "class='form-control picker-select' multiple");?>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->workType;?></span>
            <?php echo html::select('workType', $lang->gantt->workTypeList, $workType, "class='form-control picker-select' multiple");?> 
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->task_estStarted;?></span>
            <div class='datepicker-wrapper datepicker-date'><?php echo html::input('task_estStarted', $task_estStarted, "class='form-control form-date' ");?></div>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->task_assignTo;?></span>
            <?php echo html::select('task_assignTo', $users, $task_assignTo, "class='form-control chosen searchSelect' multiple width='300px'");?>
            <?php echo html::hidden('task_finishedBy', $task_finishedBy);?>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->dept_id;?></span>
            <?php echo html::select('dept_id', array(''=>'')+$depts, $dept_id, "class='form-control chosen searchSelect'");?>
          </div>
        </div>
      </div>

      <div style='height: 2px' ></div>
      <div class="row">
        <div class='col-sm-4'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->excutionId;?></span>
            <?php echo html::input('excutionId', $excutionId, "class='form-control' placeholder='{$lang->gantt->queryTips}'");?>
          </div>
        </div>
        <div class='col-sm-2'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->productId;?></span>
            <?php echo html::select('productId', array(''=>'')+$productPairs, $productId, "class='form-control chosen searchSelect' ");?>
          </div>
        </div>
        <div class='col-sm-4'>
          <div class='input-group'>      
          </div><div class='input-group'>  
            <span class='input-group-addon'><?php echo $lang->gantt->query->storyId;?></span>
            <?php echo html::input('storyId', $storyId, "class='form-control' placeholder='{$lang->gantt->stroyQueryTips}'");?>
          </div>
        </div>

      </div>
      <div style='height: 2px' ></div>
      <div class="row">
        <div class='col-sm-4'>
          <div class='input-group'>
           
          </div>
        </div>
        <div class='col-sm-4'>
          <div class='input-group' >
            <?php echo html::commonButton($label = '查询', $misc = " id='query' onclick='if (validate()) query();'", $class = 'btn btn-primary', $icon = '');?>
            <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class='input-group-addon'></span>
            <?php echo html::commonButton($label = '重置', $misc = " id='reset' onclick='reset();'", $class = 'btn', $icon = '');?>
            <?php if(!empty($taskList)):?>
            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class='input-group-addon'></span>            
            <?php echo html::commonButton($label = '导出', $misc = " id='export' onclick='exportGantt();'", $class = 'btn', $icon = '');?>
            <?php endif;?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>