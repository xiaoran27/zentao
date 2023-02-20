<?php
include '../../../../../module/story/control.php';

class myStory extends story
{


    /**
     * get a purchaser.
     *
     * @param  string $name
     * @access public
     * @return void
     */
    public function getPurchaserList($codeOrName='')
    {
        $common = $this->loadModel('common'); //
        $data = $common->getPurchaserList($codeOrName);
        // $common->log('getPurchaserList: ' . json_encode(array("codeOrName"=>$codeOrName,"data"=>$data),JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        
       
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }

}