<?php

class myStory extends story
{


    /**
     * add a purchaser.
     *
     * @param  string $name
     * @access public
     * @return void
     */
    public function addPurchaser($name,$category='B100')
    {
        $id = $this->story->addPurchaser($name,'',$category);
        return $this->send(array('result' => ($id>0?'success':'failure'), 'message' => 'name=' . $name, 'id' => $id));
        
    }

}