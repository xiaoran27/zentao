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
    public function getPurchaserList($code='')
    {
        $common = $this->loadModel('common'); //
        
        $common->log('getPurchaserList: ' . json_encode(array("code"=>$code),JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        
        
        $data = $common->getPurchaserList($code);

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }

}