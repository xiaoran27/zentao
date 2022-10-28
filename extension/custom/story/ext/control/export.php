<?php

class myStory extends story
{
    
    /**
     * get data to export
     *
     * @param  int    $productID
     * @param  string $orderBy
     * @param  int    $executionID
     * @param  string $browseType
     * @param  string $storyType requirement|story
     * @access public
     * @return void
     */
    public function export($productID, $orderBy, $executionID = 0, $browseType = '', $storyType = 'story')
    {
        /* format the fields of every story in order to export data. */
        if($_POST)
        {
            $this->session->set('storyPortParams', array('productID' => $productID, 'executionID' => $executionID));
            /* Create field lists. */
            $this->post->set('rows', $this->story->getExportStorys($executionID, $orderBy));
            $this->fetch('port', 'export', 'model=story');
        }

        $fileName = $storyType == 'requirement' ? $this->lang->URCommon : $this->lang->SRCommon;
        if($executionID)
        {
            $executionName = $this->dao->findById($executionID)->from(TABLE_PROJECT)->fetch('name');
            $fileName      = $executionName . $this->lang->dash . $fileName;
        }
        else
        {
            $productName = $productID ? $this->dao->findById($productID)->from(TABLE_PRODUCT)->fetch('name') : $this->lang->product->all;
            if(isset($this->lang->product->featureBar['browse'][$browseType]))
            {
                $browseType = $this->lang->product->featureBar['browse'][$browseType];
            }
            else
            {
                $browseType = isset($this->lang->product->moreSelects[$browseType]) ? $this->lang->product->moreSelects[$browseType] : '';
            }

            $fileName = $productName . $this->lang->dash . $browseType . $fileName;
        }

        if($storyType == 'story' ) $this->config->story->exportFields = str_replace("responseResult,","",$this->config->story->exportFields.',');

        $this->view->fileName        = $fileName;
        $this->view->allExportFields = $this->config->story->exportFields;
        $this->view->customExport    = true;
        $this->display();
    }

}
