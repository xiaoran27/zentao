<?php


    public function getDataOfPurchaser()
    {
        return $this->loadExtension('bytenew')->getDataOfPurchaser();
    }

    /**
     * Get report data of purchaser
     *
     * @access public
     * @return array
     */
    // Deprecated @see class/bytenew.class.php
    public function getDataOfPurchaser0()
    {
        $datas = $this->dao->select('purchaser as name, count(purchaser) as value')->from(TABLE_STORY)
            ->where($this->reportCondition())
            ->groupBy('purchaser')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        // foreach($datas as $purchaser => $data) $data->name = $this->lang->story->purchaserList[$purchaser] != '' ? $this->lang->story->purchaserList[$purchaser] : $purchaser;
        return $datas;
    }

