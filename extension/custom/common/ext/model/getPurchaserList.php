<?php

public function getPurchaserList($where='status=0')
{
    static $TABLE_PURCHASER  = 'zt_purchaser';
    $purchaserList = array(''=>'');
    $datas = $this->dao->select('*')->from($TABLE_PURCHASER)->where($where)->orderby('mtime DESC')->fetchAll();
    if(!$datas) return $purchaserList;
    foreach($datas as $key => $data) $purchaserList[$data->code]=$data->name;
    
    return $purchaserList;
}
