<?php

public function getPurchaserList($code='')
{
    static $TABLE_PURCHASER  = 'zt_purchaser';
    $purchaserList = array(''=>'');
    $where='status=0' . (empty($code)?"":" and code ='$code'");
    $datas = $this->dao->select('*')->from($TABLE_PURCHASER)->where($where)->fetchAll();
    if(!$datas) return $purchaserList;
    foreach($datas as $key => $data) {
        $value = $data->name ;
        if (!empty($code)) {
            $value .=  ','.$data->category ; 
            $value .=  ( isset($data->scoreNum) ? ','.$data->scoreNum  : '' );
        }

        $purchaserList[$data->code] =  $value ; 
    }
    
    return $purchaserList;
}
