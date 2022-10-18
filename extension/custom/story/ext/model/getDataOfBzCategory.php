<?php

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

