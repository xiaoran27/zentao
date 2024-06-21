<?php
include '../../../../../module/project/control.php';

class myProject extends project
{


    /**
     * get project pairs by program.
     *
     * @param  string $programId
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function ajaxGetPairsByProgram($programId)
    {
        $projectPairs = $this->project->getPairsByProgram($programId, $status = 'all', $isQueryAll = true, $orderBy = 'order_asc', $excludedModel = '', $model = '', $param = '');
        
        return print(html::select('projectId', array(''=>'')+$projectPairs, '', "class='form-control chosen searchSelect' multiple"));
    }

}
