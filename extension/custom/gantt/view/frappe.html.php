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
  <?php include 'conditions.html.php';?>
</div>
<br />
<div id='dataContent' class='main-row'>
  <div class='main-col'><div class='cell'>
  <?php if(empty($taskList)):?>
      <p><span class="text-muted"><?php echo $lang->error->noData;?></span></p>
  <?php else:?>
      <div id="gantt" class="gantt"></div><?php //html2canvas 不支持svg ?>
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
