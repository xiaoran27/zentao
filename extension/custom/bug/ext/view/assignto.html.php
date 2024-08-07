<?php
/**
 * The complete file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Jia Fu <fujia@cnezsoft.com>
 * @package     task
 * @version     $Id: complete.html.php 935 2010-07-06 07:49:24Z jajacn@126.com $
 * @link        http://www.zentao.net
 */
?>
<?php 
include '../../../../../module/common/view/header.html.php';
include '../../../../../module/common/view/kindeditor.html.php';
js::set('holders', $lang->bug->placeholder);
js::set('page', 'assignedto');
?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2>
        <span class='label label-id'><?php echo $bug->id;?></span>
        <?php echo isonlybody() ? ('<span title="' . $bug->title . '">' . $bug->title . '</span>') : html::a($this->createLink('bug', 'view', 'bug=' . $bug->id), $bug->title);?>

        <?php if(!isonlybody()):?>
        <small><?php echo $lang->arrow . $lang->bug->assignBug;?></small>
        <?php endif;?>
      </h2>
    </div>
    <form method='post' target='hiddenwin'>
      <table class='table table-form'>
        <tr>
          <th><?php echo $lang->bug->feedbackTime;?></th>
          <td><?php echo html::input('feedbackTime', helper::isZeroDate($bug->feedbackTime) ? helper::now() : $bug->feedbackTime, "class='form-control form-datetime'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->bug->collectTime;?></th>
          <td><?php echo html::input('collectTime', helper::isZeroDate($bug->collectTime) ? helper::now() : $bug->collectTime, "class='form-control form-datetime'");?></td>
        </tr>
        <tr>
          <th class='w-80px'><?php echo $lang->bug->assignBug;?></th>
          <td class='w-p25-f'><?php echo html::select('assignedTo', $users, $bug->assignedTo, "class='form-control chosen'");?></td><td></td>
        </tr>  
        <tr class='hide'>
          <th><?php echo $lang->bug->status;?></th>
          <td><?php echo html::hidden('status', $bug->status);?></td>
        </tr>
        <?php $this->printExtendFields($bug, 'table');?>
        <tr>
          <th><?php echo $lang->bug->mailto;?></th>
          <td colspan='2'><?php echo html::select('mailto[]', $users, str_replace(' ', '', $bug->mailto), 'class="form-control chosen" multiple');?></td>
        </tr>
        <tr>
          <th><?php echo $lang->comment;?></th>
          <td colspan='2'><?php echo html::textarea('comment', '', "rows='6' class='form-control'");?></td>
        </tr>
        <tr>
          <td colspan='3' class='text-center form-actions'><?php echo html::submitButton() . html::linkButton($lang->goback, $this->server->http_referer, 'self', '', 'btn btn-wide');?></td>
        </tr>
      </table>
    </form>
    <hr class='small' />
    <div class='main'><?php include '../../../../../module/common/view/action.html.php';?></div>
  </div>
</div>
<?php include '../../../../../module/common/view/footer.html.php';?>
