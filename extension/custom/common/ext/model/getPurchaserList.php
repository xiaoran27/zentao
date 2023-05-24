<?php

public function getPurchaserList($codeOrName='')
{
    static $TABLE_PURCHASER  = 'zt_purchaser';
    $purchaserList = array(''=>'');
    $where='status=0' . (empty($codeOrName)?"":" and ( code ='$codeOrName' or name ='$codeOrName' ) ");
    $datas = $this->dao->select('*')->from($TABLE_PURCHASER)->where($where)->fetchAll();
    if(!$datas) return $purchaserList;
    foreach($datas as $key => $data) {
        // $this->loadModel('common')->log(json_encode($data,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        if (empty($codeOrName)) {
            $value = $data->name ;
            $purchaserList[$data->code] =  $value ; 
        }else{
            $value = $data->code ;
            $value .=  ','.$data->name ;
            $value .=  ','.$data->category ; 
            $value .=  ( isset($data->scoreNum) ? ','.$data->scoreNum  : '' );
            $purchaserList[$codeOrName] =  $value ; 
        }

       
    }
    
    return $purchaserList;
}
