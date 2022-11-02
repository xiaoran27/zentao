<?php

class bytenewBug extends BugModel
{

    
    /**
     * Build search form.
     *
     * @param  int    $productID
     * @param  array  $products
     * @param  int    $queryID
     * @param  string $actionURL
     * @param  int    $branch
     * @access public
     * @return void
     */
    public function buildSearchForm($productID, $products, $queryID, $actionURL, $branch = 0)
    {
        $projectID     = $this->lang->navGroup->bug == 'qa' ? 0 : $this->session->project;
        $productParams = ($productID and isset($products[$productID])) ? array($productID => $products[$productID]) : $products;
        $productParams = $productParams + array('all' => $this->lang->bug->allProduct);
        $projectParams = $this->getProjects($productID);
        $projectParams = $projectParams + array('all' => $this->lang->bug->allProject);

        /* Get all modules. */
        $modules = array();
        $this->loadModel('tree');
        if($productID) $modules = $this->tree->getOptionMenu($productID, 'bug', 0, $branch);
        if(!$productID)
        {
            foreach($products as $id => $productName) $modules += $this->tree->getOptionMenu($id, 'bug');
        }

        $this->config->bug->search['actionURL'] = $actionURL;
        $this->config->bug->search['queryID']   = $queryID;
        if($this->config->systemMode == 'new') $this->config->bug->search['params']['project']['values'] = $projectParams;
        $this->config->bug->search['params']['product']['values']       = $productParams;
        $this->config->bug->search['params']['plan']['values']          = $this->loadModel('productplan')->getPairs($productID);
        $this->config->bug->search['params']['module']['values']        = $modules;
        $this->config->bug->search['params']['execution']['values']     = $this->loadModel('product')->getExecutionPairsByProduct($productID, 0, 'id_desc', $projectID);
        $this->config->bug->search['params']['severity']['values']      = array(0 => '') + $this->lang->bug->severityList; //Fix bug #939.
        $this->config->bug->search['params']['openedBuild']['values']   = $this->loadModel('build')->getBuildPairs($productID, 'all', 'withbranch');
        $this->config->bug->search['params']['resolvedBuild']['values'] = $this->config->bug->search['params']['openedBuild']['values'];
        if($this->session->currentProductType == 'normal')
        {
            unset($this->config->bug->search['fields']['branch']);
            unset($this->config->bug->search['params']['branch']);
        }
        else
        {
            $this->config->bug->search['fields']['branch'] = $this->lang->product->branch;
            $this->config->bug->search['params']['branch']['values']  = array('' => '', 0 => $this->lang->branch->main) + $this->loadModel('branch')->getPairs($productID, 'noempty') + array('all' => $this->lang->branch->all);
        }

        $this->config->bug->search['fields']['purchaser']  = $this->lang->bug->purchaser;
        $purchaserList=$this->loadModel('common')->getPurchaserList();
        $this->config->bug->search['params']['purchaser']  = array('operator' => '=',       'control' => 'select',  'values' => $purchaserList);

        $this->loadModel('search')->setSearchParams($this->config->bug->search);
    }

    public function getPurchaserList()
    {
        $purchaserList=$this->loadModel('common')->getPurchaserList();
        
        // $this->loadModel('common')->log(json_encode($this->lang->bug->purchaserList,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        $this->lang->bug->purchaserList = $purchaserList;
        $this->config->bug->purchasers = array_keys($purchaserList);
        $this->config->bug->search['params']['purchaser']  = array('operator' => '=',       'control' => 'select',  'values' => $purchaserList);
        $this->config->bug->datatable->fieldList['purchaser']['dataSource'] = $this->config->bug->purchasers;
        // $this->loadModel('common')->log(json_encode($this->lang->bug->purchaserList,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

        return $purchaserList;
    }

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

        $purchaserList    = $this->loadModel('common')->getPurchaserList();
        $this->config->bug->datatable->fieldList['occursEnv']['dataSource'] = array_keys($purchaserList);

        foreach($datas as $purchaser => $data) $data->name = $purchaserList[$purchaser] != '' ? $purchaserList[$purchaser] : $purchaser;
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
        $sql=$this->dao->select("substring_index(substring_index(concat(',',a.occursEnv), ',', b.help_topic_id + 1), ',', - 1) AS occursEnv")
            ->from(TABLE_BUG)->alias('a inner join mysql.help_topic b')
            ->on("b.help_topic_id < (length(concat(',',a.occursEnv)) - length(REPLACE(concat(',',a.occursEnv), ',', '')) + 1)")
            ->where($this->reportCondition())->get();
        // $this->loadModel('common')->log(print_r($sql, true));
        // $sql2 = $this->dao->select("occursEnv as name, count(occursEnv) as value ")->from('('. $sql.')' )->alias('me')->groupBy('occursEnv')->orderBy('value DESC')->get();
        // $this->loadModel('common')->log(print_r($sql2, true));

        $datas = $this->dao->select("occursEnv as name, count(1) as value ")->from('('. $sql.')' )->alias('me')->where('me.occursEnv !=""')->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();
        foreach($datas as $occursEnv => $data) $data->name = $this->lang->bug->occursEnvList[$occursEnv] != '' ? $this->lang->bug->occursEnvList[$occursEnv] : $occursEnv;
       
        //occursEnv 无值的情况
        $datas2 = $this->dao->select("'NA' as name, count(1) as value")->from(TABLE_BUG)
            ->where($this->reportCondition())->andwhere("length(ifnull(occursEnv,''))=0")
            ->groupBy('name')->orderBy('value DESC')->fetchAll('name');

        return $datas +  $datas2;
    }


}