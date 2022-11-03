<?php

include '../../../../../module/datatable/control.php';

class myDatatable extends datatable
{
   

    /**
     * custom fields.
     *
     * @param  string $module
     * @param  string $method
     * @param  string $extra
     * @access public
     * @return void
     */
    public function ajaxCustom($module, $method, $extra = '')
    {
        $target = $module . ucfirst($method);
        $mode   = isset($this->config->datatable->$target->mode) ? $this->config->datatable->$target->mode : 'table';
        $key    = $mode == 'datatable' ? 'cols' : 'tablecols';

        if($module == 'testtask')
        {
            $this->loadModel('testcase');
            $this->app->loadConfig('testtask');
            $this->config->testcase->datatable->defaultField = $this->config->testtask->datatable->defaultField;
            $this->config->testcase->datatable->fieldList['actions']['width'] = '100';
            $this->config->testcase->datatable->fieldList['status']['title']  = $this->lang->testcase->executionStatus;
            $this->config->testcase->datatable->fieldList['status']['width']  = '90';
        }
        if($module == 'testcase')
        {
            $this->loadModel('testcase');
            unset($this->config->testcase->datatable->fieldList['assignedTo']);
        }

        $this->view->module = $module;
        $this->view->method = $method;
        $this->view->mode   = $mode;

        $module  = zget($this->config->datatable->moduleAlias, "$module-$method", $module);
        $setting = '';
        if(isset($this->config->datatable->$target->$key)) $setting = $this->config->datatable->$target->$key;
        if(empty($setting))
        {
            $this->loadModel($module);
            $setting = json_encode($this->config->$module->datatable->defaultField);
        }

        $cols = $this->datatable->getFieldList($module);
        
        if($extra == 'requirement')
        {
            unset($cols['plan']);
            unset($cols['stage']);
            unset($cols['taskCount']);
            unset($cols['bugCount']);
            unset($cols['caseCount']);
            $cols['title']['title'] = str_replace($this->lang->SRCommon, $this->lang->URCommon, $this->lang->story->title);
        } else {
            unset($cols['responseResult']);
        }

        // $this->loadModel('common')->log(json_encode($cols,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        $this->view->cols    = $cols;
        $this->view->setting = $setting;
        $this->display();
    }
}
