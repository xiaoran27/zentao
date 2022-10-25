<?php

class bytenewBug extends BugModel
{

        /**
     * Get report data of purchaser
     *
     * @access public
     * @return array
     */
    public function getDataOfPurchaser()
    {
        $datas = $this->dao->select('purchaser as name, count(purchaser) as value')->from(TABLE_BUG)
            ->where($this->reportCondition())
            ->groupBy('purchaser')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        // foreach($datas as $purchaser => $data) $data->name = $this->lang->story->purchaserList[$purchaser] != '' ? $this->lang->story->purchaserList[$purchaser] : $purchaser;
        return $datas;
    }

            /**
     * Get report data of occursEnv
     *
     * @access public
     * @return array
     */
    public function getDataOfOccursEnv()
    {
        $datas = $this->dao->select('occursEnv as name, count(occursEnv) as value')->from(TABLE_BUG)
            ->where($this->reportCondition())
            ->groupBy('occursEnv')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $occursEnv => $data) $data->name = $this->lang->story->occursEnvList[$occursEnv] != '' ? $this->lang->story->occursEnvList[$occursEnv] : $occursEnv;
        return $datas;
    }


}