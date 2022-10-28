<?php

class bytenewStory extends StoryModel
{

    
    /**
     * Get report data of bzCategory
     *
     * @access public
     * @return array
     */
    public function getDataOfBzCategory()
    {
        $datas = $this->dao->select('bzCategory as name, count(bzCategory) as value')->from(TABLE_STORY)
            ->where($this->reportCondition())
            ->groupBy('bzCategory')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $bzCategory => $data) $data->name = $this->lang->story->bzCategoryList[$bzCategory] != '' ? $this->lang->story->bzCategoryList[$bzCategory] : $bzCategory;
        return $datas;
    }

        /**
     * Get report data of prCategory
     *
     * @access public
     * @return array
     */
    public function getDataOfPrCategory()
    {
        $datas = $this->dao->select('prCategory as name, count(prCategory) as value')->from(TABLE_STORY)
            ->where($this->reportCondition())
            ->groupBy('prCategory')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $prCategory => $data) $data->name = $this->lang->story->prCategoryList[$prCategory] != '' ? $this->lang->story->prCategoryList[$prCategory] : $prCategory;
        return $datas;
    }

        /**
     * Get report data of purchaser
     *
     * @access public
     * @return array
     */
    public function getDataOfPurchaser()
    {
        $datas = $this->dao->select('purchaser as name, count(purchaser) as value')->from(TABLE_STORY)
            ->where($this->reportCondition())
            ->groupBy('purchaser')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        // foreach($datas as $purchaser => $data) $data->name = $this->lang->story->purchaserList[$purchaser] != '' ? $this->lang->story->purchaserList[$purchaser] : $purchaser;
        return $datas;
    }


        /**
     * Get report data of uatDate
     *
     * @access public
     * @return array
     */
    public function getDataOfUatDate()
    {
        $datas = $this->dao->select('uatDate as name, count(uatDate) as value')->from(TABLE_STORY)
            ->where($this->reportCondition())
            ->groupBy('uatDate')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        // foreach($datas as $uatDate => $data) $data->name = $this->lang->story->purchaserList[$uatDate] != '' ? $this->lang->story->purchaserList[$uatDate] : $uatDate;
        return $datas;
    }

    /**
     * Get report data of responseResult
     *
     * @access public
     * @return array
     */
    public function getDataOfResponseResult()
    {
        $datas = $this->dao->select('responseResult as name, count(1) as value')->from(TABLE_STORY)
            ->where($this->reportCondition())
            ->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $name => $data) $data->name = $this->lang->story->responseResultList[$name] != '' ? $this->lang->story->responseResultList[$name] : $name;
        return $datas;
    }

}