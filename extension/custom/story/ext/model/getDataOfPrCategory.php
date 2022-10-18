<?php

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
        foreach($datas as $prCategory => $data) $data->name = $this->lang->story->bzCategoryList[$prCategory] != '' ? $this->lang->story->bzCategoryList[$prCategory] : $prCategory;
        return $datas;
    }

