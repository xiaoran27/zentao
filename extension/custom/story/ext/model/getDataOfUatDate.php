<?php

    public function getDataOfUatDate()
    {
        return $this->loadExtension('bytenew')->getDataOfUatDate();
    }

    /**
     * Get report data of uatDate
     *
     * @access public
     * @return array
     */
    // Deprecated @see class/bytenew.class.php
    public function getDataOfUatDate0()
    {
        $datas = $this->dao->select('uatDate as name, count(uatDate) as value')->from(TABLE_STORY)
            ->where($this->reportCondition())
            ->groupBy('uatDate')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        // foreach($datas as $uatDate => $data) $data->name = $this->lang->story->purchaserList[$uatDate] != '' ? $this->lang->story->purchaserList[$uatDate] : $uatDate;
        return $datas;
    }

