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
<style>#mainContent > .side-col.col-lg{width: 0px}</style>
<style>.hide-sidebar #sidebar{width: 0 !important}</style>
<style>.clearfix{width: 300 !important}</style>
<!-- <style>#mainContent > .row{height: 235px}</style> -->

<?php include 'conditions.html.php';?>
<br />

<?php if(!empty($taskList)):?>
<div id='menuContent' class='main-row'><div class='main-col'><div class='cell'>
<div class="row"><div class='col-sm-4'><div class='input-group'>
  <div id='showMenu' class="btn-toolbar pull-left">
    <div id="showGantt" class="btn btn-link btn-active-text"><span class="text">甘特图</span></div>
    <div id="showTable" class="btn btn-link "><span class="text">数据表</span></div>
  </div>
</div></div></div>
</div></div></div>
<?php endif;?>

<?php if(empty($taskList)):?>
  <div id='dataContent' class='main-row'><div class='main-col'><div class='cell'>
    <div class="table-empty-tip">
      <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
    </div>
  </div></div></div>
<?php else:?>
  <div id='ganttContent' class='main-row' ><div class='main-col'><div class='cell'>
    <div id="gantt" class="gantt"></div><?php //html2canvas 不支持svg ?>
  </div></div></div>
  <div id='dataContent' class='main-row' style="display:none"><div class='main-col'><div class='cell'>
    <form class="main-table table-task skip-iframe-modal" method="post" id='ganttTaskForm'>
    <div class="table-responsive">
      <table class=' main-table table datatable table-hover table-fixed data-fixed-left-width=550 data-fixed-right-width=180' id='taskList'>
        <thead>
          <tr class='text-center'>
            <th class='c-id'> <?php echo $lang->gantt->id;?></th>
            <th class='c-name'> <?php echo $lang->gantt->name;?></th>
            <th class='c-status'> <?php echo $lang->gantt->status;?></th>
            <th class='c-myBegin'> <?php echo $lang->gantt->myBegin;?></th>
            <th class='c-myEnd'> <?php echo $lang->gantt->myEnd;?></th>
            <th class='c-progress'> <?php echo $lang->gantt->progress;?></th>
            <th class='c-milestone'> <?php echo $lang->gantt->milestone;?></th>
            <th class='c-realname'> <?php echo $lang->gantt->realname;?></th>
            <th class='c-dept_name'> <?php echo $lang->gantt->dept_name;?></th>
            <th class='c-estimate'> <?php echo $lang->gantt->estimate;?></th>
            <th class='c-consumed'> <?php echo $lang->gantt->consumed;?></th>
            <th class='c-type'> <?php echo $lang->gantt->type;?></th>
            <th class='c-parent' > <?php echo $lang->gantt->parent;?></th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 0; $children=0;
            $isParent = false;
            $isParent = $isParent or empty($rowtype) or ($rowtype == 'all') ;
            $isParent = $isParent or ( strpos(",$rowtype,", ',project,') !== false and strpos(",$rowtype,", ',execution,') !== false and strpos(",$rowtype,", ',task,') !== false );
          ?>
          <?php foreach($taskList as $task):?>
            <?php 
            $id = substr($task->id,0); $parent=substr($task->parent,0);
            $dataleft=1;
            $trClass = "text-center";
            // $isParent_ = ( !( $isParent and $parent=='P') and $task->children>0 );
            $isParent_ = ( $task->children>0 );
            if ( $isParent_ ){
              $children=$task->children;
              $dataleft=8;
              $trClass .= " table-parent ";
            }
            if(!empty($task->parent)){
              $dataleft=4;
              $i += 1;
              $trClass .=  " table-children parent-{$parent}";
              if ($i == 1 and $i==$children){
                $i = 0; $children=0;
                $trClass .=  " table-child-top table-child-bottom";
              }elseif ($i < $children){
                $trClass .=  " table-child-top";
              }elseif ($i==$children){
                $i = 0; $children=0;
                $trClass .=  " table-child-bottom";
              }
            }
            echo "<tr class='{$trClass}' data-left='{$dataleft}'  data-id='{$id}' data-status='{$task->status}' data-estimate='{$task->estimate}' data-consumed='{$task->consumed}' >";
            ?>
            <td><?php echo $task->id;?></td>
            <td class='c-name text-left <?php echo ( $isParent_ )?'has-child':'';?>' title='<?php echo $task->name;?>'>
              <a class="iframe" data-width="90%" href="<?php echo "{$this->config->webRoot}".$task->url; ?>"><?php echo $task->name;?></a>
              <?php if( $isParent_ ) echo "<a class='task-toggle' data-id='{$id}'><i class='icon icon-angle-double-right'></i></a>";?></td>
            <td ><?php echo $task->status;?></td>
            <td ><?php echo $task->start;?></td>
            <td ><?php echo $task->end;?></td>
            <td ><?php echo $task->progress;?></td>
            <td ><?php echo $task->milestone;?></td>
            <td ><?php echo $task->realname;?></td>
            <td><?php echo $task->deptname;?></td>
            <td ><?php echo $task->estimate;?></td>
            <td ><?php echo $task->consumed;?></td>
            <td ><?php echo $task->type;?></td>
            <td><?php  "{$task->parent}({$task->children})" ;?></td>
            
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </form>
  </div></div></div>


<?php endif;?>
  

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

  /* .gantt .bar.important {
    fill: #94c4f4;
  } */
  /* .gantt-container .popup-wrapper {
    padding: 0 5px;
  } */
  /* .gantt .bar {
      fill: lightgray !important;
  } */
  .gantt .bar-progress {
      fill: lightseagreen !important;
  }
  
  .bar-label {
    fill: #000  !important;
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
          // header_height: 65,
          // column_width: 30,
          // step: 24,
          view_modes: ["Day", "Week", "Month"],
          // bar_height: 30,
          // bar_corner_radius: 3,
          // arrow_curve: 5,
          // padding: 18,
          // view_mode: "Day",
          date_format: "YYYY-MM-DD",
          language: "zh",
          readonly: true,
          // highlight_weekend: true,
          // scroll_to: 'start',  // start, today, yyyy-mm-dd
          // lines: 'both',
          // auto_move_label: true,
          today_button: false,
          // view_mode_select: true,
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

              const dateChanged = task.start !== task.start__ || task.end !== task.end__;
              const resources = task.owner == undefined || task.owner == '' ? '':task.realname+'('+task.owner+')';
              const dependencies = task.dependencies == undefined || task.dependencies == '' ? '':task.dependencies;
              const begin_date = (task.start).substring(2,10);
              const end_date = (task.end).substring(2,10);
              const begin_date__ = (task.start__).substring(2,10);
              const end_date__ = (task.end__).substring(2,10);
              // const begin_date = date_utils.format(task._start,'MM-DD');
              // const end_date = date_utils.format(task._end,'MM-DD');
              const estimate = task.estimate == undefined ? 'NA':task.estimate;
              const consumed = task.consumed == undefined ? 'NA':task.consumed;
              const progress = task.progress == undefined ? ( (/^\d+$/.test(consumed) && /^\d+$/.test(estimate) && estimate>0)?(consumed/estimate*100).toFixed(2):'NA'):task.progress;
              return `
                  <p><strong>ID:</strong> ${task.id} <strong>状态:</strong> ${task.status} <strong>依赖:</strong> ${dependencies} <strong>需求:</strong> ${task.story}</p>
                  <p><strong>资源:</strong> ${resources} <strong>部门:</strong> ${task.deptname}</p>
                  <p><strong>起止日期` +(dateChanged?'':`(${task.duration__})`)+`:</strong> ${begin_date}~${end_date}</p>
                  ` +(dateChanged?`<p><strong>实际起止(${task.duration__}):</strong> ${begin_date__}~${end_date__}</p>`:'') +`
                  <p><strong>进度:</strong> ${progress}% <strong>预估:</strong> ${estimate}h,<strong>消耗:</strong> ${consumed}h</p>
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
