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
    public function getDataOfOccursEnv0()
    {

        $datas = $this->dao->select('occursEnv as name, count(1) as value')->from(TABLE_BUG)
            ->where($this->reportCondition())
            ->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        foreach($datas as $occursEnv => $data) {
            $occursEnvs = explode(',', $occursEnv);
            $names = '';
            foreach( $occursEnvs as $one ) {
                $one = trim($one);
                if(empty($one))   continue;
                if(!empty($names))  $names = $names.',';
                $names = $names. ($this->lang->bug->occursEnvList[$one] != '' ? $this->lang->bug->occursEnvList[$one] : $one );
            }
            $data->name = $names;
        }

        return $datas;
    }

    public function getDataOfOccursEnv()
    {
        /*
        select occursEnv as name, count(occursEnv) as value from (
        SELECT substring_index(substring_index(a.occursEnv, ',', b.help_topic_id + 1), ',', - 1) AS occursEnv
        FROM zt_bug as a 
        INNER JOIN mysql.help_topic b
            ON b.help_topic_id < (length(a.occursEnv) - length(REPLACE(a.occursEnv, ',', '')) + 1) where occursEnv is not null
        ) as me group by occursEnv ;
        */

        //occursEnv 必须有值
        $sql=$this->dao->select("substring_index(substring_index(a.occursEnv, ',', b.help_topic_id + 1), ',', - 1) AS occursEnv")
            ->from(TABLE_BUG)->alias('a inner join mysql.help_topic b')
            ->on("b.help_topic_id < (length(a.occursEnv) - length(REPLACE(a.occursEnv, ',', '')) + 1)")
            ->where($this->reportCondition())->get();
        // $this->loadModel('common')->log(print_r($sql, true));
        // $sql2 = $this->dao->select("occursEnv as name, count(occursEnv) as value ")->from('('. $sql.')' )->alias('me')->groupBy('occursEnv')->orderBy('value DESC')->get();
        // $this->loadModel('common')->log(print_r($sql2, true));

        $datas = $this->dao->select("occursEnv as name, count(1) as value ")->from('('. $sql.')' )->alias('me')->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $occursEnv => $data) $data->name = $this->lang->bug->occursEnvList[$occursEnv] != '' ? $this->lang->bug->occursEnvList[$occursEnv] : $occursEnv;
       
        //occursEnv 无值的情况
        $datas2 = $this->dao->select("'NA' as name, count(1) as value")->from(TABLE_BUG)
            ->where($this->reportCondition())->andwhere("length(ifnull(occursEnv,''))=0")
            ->groupBy('name')->orderBy('value DESC')->fetchAll('name');

        return $datas +  $datas2;
    }


}