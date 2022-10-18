<?php


    public function getDataOfPrCategory()
    {
        return $this->loadExtension('bytenew')->getDataOfPrCategory();
    }

    /**
     * Get report data of prCategory
     *
     * @access public
     * @return array
     */
    // Deprecated @see class/bytenew.class.php
    public function getDataOfPrCategory0()
    {
        $datas = $this->dao->select('prCategory as name, count(prCategory) as value')->from(TABLE_STORY)
            ->where($this->reportCondition())
            ->groupBy('prCategory')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $prCategory => $data) $data->name = $this->lang->story->prCategoryList[$prCategory] != '' ? $this->lang->story->prCategoryList[$prCategory] : $prCategory;
        return $datas;
    }

