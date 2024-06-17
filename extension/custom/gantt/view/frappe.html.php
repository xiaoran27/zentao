<?php 

if(!empty($taskList)):
  $jsroot = $config->webRoot . "js/";
  $timestamp = $config->debug?time():$config->vision;

  // js::import($jsRoot . "moment/moment.min.js");
  css::import($jsroot.'gantt/frappe-gantt.css');
  js::import($jsroot.'gantt/frappe-gantt.js');

  js::set('taskList', $taskList);
endif;
?> 
<?php include '../../../../module/common/view/header.html.php';?>
<?php include '../../../../module/common/view/datepicker.html.php';?>
<?php if($this->config->edition != 'open'):?>
<style>#mainContent > .side-col.col-lg{width: 235px}</style>
<style>.hide-sidebar #sidebar{width: 0 !important}</style>
<?php endif;?>
<style>#mainContent > .row{height: 235px}</style>
<div id='mainContent' class='main-row'>
  <div class='main-col' id='conditions'>
    <div class='cell'>
      <div class="row">
        <div class='col-sm-4'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->programId;?></span>
            <?php echo html::select('programId', array(''=>'')+$programPairs, $programId, "class='form-control chosen searchSelect' ");?>
          </div>
        </div>
        <div class='col-sm-4'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->projectId;?></span>
            <?php echo html::select('projectId', array(''=>'')+$projectPairs, $projectId, "class='form-control chosen searchSelect' multiple ");?>
          </div>
        </div>
        <div class='col-sm-4'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->projectEnd;?></span>
            <div class='datepicker-wrapper datepicker-date'><?php echo html::input('projectEnd', $projectEnd, "class='form-control form-date' ");?></div>
          </div>
        </div>
      </div>
      <div style='height: 2px' ></div>
      <div class="row">
        <div class='col-sm-4'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->projectPM;?></span>
            <?php echo html::select('projectPM', $users, $projectPM, "class='form-control chosen searchSelect' multiple");?>
          </div>
        </div>
        <div class='col-sm-4'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->projectStatus;?></span>
            <?php echo html::select('projectStatus', $lang->gantt->projectStatusList, empty($projectStatus)?'wait,doing':$projectStatus, "class='form-control chosen' multiple");?>
          </div>
        </div>
        <div class='col-sm-4'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->rowtype;?></span>
            <?php echo html::select('rowtype', $lang->gantt->typeList, $rowtype, "class='form-control chosen' multiple");?>
          </div>
        </div>
      </div>
      <div style='height: 2px' ></div>
      <div class="row">
        <div class='col-sm-4'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->task_assignTo;?></span>
            <?php echo html::select('task_assignTo', $users, $task_assignTo, "class='form-control chosen searchSelect' multiple");?>
            <?php echo html::hidden('task_estStarted', $task_estStarted);?>
            <?php echo html::hidden('task_finishedBy', $task_finishedBy);?>
          </div>
        </div>
        <div class='col-sm-4'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->excutionId;?></span>
            <?php echo html::input('excutionId', $excutionId, "class='form-control' placeholder='{$lang->gantt->queryTips}'");?>
          </div>
        </div>
        <div class='col-sm-4'>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->gantt->query->storyId;?></span>
            <?php echo html::input('storyId', $storyId, "class='form-control' placeholder='{$lang->gantt->queryTips}'");?>
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
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<br />
<div id='dataContent' class='main-row'>
  <div class='main-col'><div class='cell'>
  <?php if(empty($taskList)):?>
      <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
  <?php else:?>
      <div><svg id="gantt" class="gantt"></svg></div>
  <?php endif;?>
  </div>
  </div>
</div>

<style>
  /* .gantt .bar-project  {
    fill: #838a9d !important;
  }

  .gantt .bar-execution  {
    fill: #c4c4c4 !important;
  }

  .gantt .bar-task {
    fill: #eee !important;
  }

  .gantt .bar-task-primary {
    fill: #f1f1f1 !important;
  }
  .gantt .bar {
      fill: #eee !important;
  }
  .gantt .bar-progress {
      fill: gray !important;
  }

  .gantt .bar-label {
      fill: rebeccapurple !important;
  } */

  .gantt-container .popup-wrapper {
    /* width: 500px; */
    padding: 0 5px;
  }
  .gantt .bar-progress {
      fill: gray !important;
  }
  .bar-label {
    text-align: left !important;
    fill: #111  !important;
    /* fill: rebeccapurple !important;
    font-size: 12px !important;
    font-weight: bold !important; */
  }
  .gantt .arrow {
    stroke: yellowgreen !important;
    stroke-width: 1px !important;
  }

</style>
<script>
<?php if(!empty($taskList)):?>
  let gantt = null;
  let date_utils = Gantt.date_utils;

  // console.log(taskList);
 
  // 等待 DOM 加载完成
  document.addEventListener('DOMContentLoaded', function() {
      
      gantt = new Gantt("#gantt", taskList, {
          header_height: 50,
          column_width: 30,
          step: 24,
          view_modes: ["Day", "Week", "Month"],
          bar_height: 20,
          bar_corner_radius: 3,
          arrow_curve: 5,
          padding: 18,
          view_mode: "Day",
          date_format: "YYYY-MM-DD",
          language: "zh",
          view_mode_select: true,
          view_mode_padding: {
            HOUR: ["7d", "7d"],
            QUARTER_DAY: ["7d", "7d"],
            HALF_DAY: ["7d", "7d"],
            DAY: ["3d", "3d"],
            WEEK: ["7d", "7d"],
            MONTH: ["1m", "1m"],
            YEAR: ["2y", "2y"],
          },
          // custom_popup_html: null,
          // popup_trigger: "click",
          custom_popup_html: function(task) {
              // the task object will contain the updated
              // dates and progress value
              // console.log(task);
              
              const resources = task.owner == undefined || task.owner == '' ? '':task.realname+'('+task.owner+')';
              const dependencies = task.dependencies == undefined || task.dependencies == '' ? '':'('+task.dependencies+')';
              const begin_date = (task.start).substring(2,10);
              const end_date = (task.end).substring(2,10);
              // const begin_date = date_utils.format(task._start,'MM-DD');
              // const end_date = date_utils.format(task._end,'MM-DD');
              const estimate = task.estimate == undefined ? 'NA':task.estimate;
              const consumed = task.consumed == undefined ? 'NA':task.consumed;
              const progress = task.progress == undefined ? ( (/^\d+$/.test(consumed) && /^\d+$/.test(estimate) && estimate>0)?(consumed/estimate*100).toFixed(2):'NA'):task.progress;
              return `
                  <p>ID: ${task.id} ${dependencies}</p>
                  <p>资源: ${resources}</p>
                  <p>起止日期: ${begin_date}~${end_date}</p>
                  <p>进度: ${progress}%</p>
                  <p>预估: ${estimate}h,消耗: ${consumed}h</p>
              `;
          },
          // on_click: function (task) {
          //     console.log(task);
          // },
          // on_date_change: function(task, start, end) {
          //     console.log(task, start, end);
          // },
          // on_progress_change: function(task, progress) {
          //     console.log(task, progress);
          // },
          // on_view_change: function(mode) {
          //     console.log(mode);
          // },
      });
      // gantt.change_view_mode();

  });
<?php endif;?>
</script>

<?php include '../../../../module/common/view/footer.html.php';?>
