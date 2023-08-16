<?php
include '../../../../../module/story/control.php';

class myStory extends story
{


    /**
     * add a purchaser.
     *
     * @param  string $name
     * @access public
     * @return void
     */
    public function addPurchaser($name,$code='',$category='B100')
    {
        // $this->loadModel('common')->log('addPurchaser name=' . json_encode($name,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

        $data = $this->story->addPurchaser($name,$code,$category);
        $type = gettype ($data);
        if ( $type == 'integer' ){
            $id = $data;
        }else{
            $id = $data->ID;
        }

        echo json_encode((array('result' => ($id>0?'success':'failure'), 'name' => $name, 'id' => $id, 'category' => $category, 'data'=>$data)) ,JSON_UNESCAPED_UNICODE );
        // return $this->send(array('result' => ($id>0?'success':'failure'), 'message' => 'name=' . $name, 'id' => $id));
        
    }

}