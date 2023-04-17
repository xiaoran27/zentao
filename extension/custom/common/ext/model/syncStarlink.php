<?php

public function syncStarlink($timeout=30)
{
    static $maxGapMinute = 5;

    // TODO: 
    // global $latestTimestamp;
    // if (empty($latestTimestamp)){
    //     $latestTimestamp = helper::now();
    // }
    // $mtime =  date_sub($latestTimestamp, date_interval_create_from_date_string("+$maxGapMinute minutes"));
    // $now =  helper::now();
    // $this->log("$latestTimestamp  ===>  $mtime > $now", __FILE__, __LINE__);
    // if ( $mtime > $now || ( date_timestamp_get($latestTimestamp) + $maxGapMinute*60*1000 > date_timestamp_get($now) ) ) {
    //     return "NA: $now - $latestTimestamp < $maxGapMinute ";
    // }



    static $TABLE_PURCHASER = "zt_purchaser";

    $diffm = $this->dao->select("timestampdiff(minute , max(mtime),now()) as diffm")->from($TABLE_PURCHASER)->fetch("diffm");
    $this->log("diffm=$diffm", __FILE__, __LINE__);
    if ( $diffm < $maxGapMinute ) {
        return "NA(minutes): $diffm < $maxGapMinute";
    }
    
    
    /*
    select customer.customer_name                               `客户名称`,
       customer.customer_company_id                         `班牛id`,
       case
           when customer.type_tag_id = 304 then '普通商家'
           when customer.type_tag_id = 476 then 'LKA商家'
           when customer.type_tag_id = 1014 then 'B100'
           when customer.type_tag_id = 1013 then 'B500' end `客户类型`
    from t_salecrm_customer customer
    where exists(select id from t_salecrm_contract where customer_id = customer.id and status in (3, 4, 6) and deleted = 0)
    */
    /*
    https://mstarlink.bytenew.com/api/starlinkApi/commonApi/listDealCustomers
    {
        "code": 0,
        "msg": "success",
        "data": [
            {
            "type": "普通商家",
            "companyId": "74",
            "customerName": "深圳全海全棉时代电子商务有限公司"
            }
        ]
    }
    */

    global $app;
    $app->loadClass('requests',true);
    
    $url = 'https://mstarlink.bytenew.com/api/starlinkApi/commonApi/listDealCustomers';
    $headers = array('Content-Type' => 'application/json','Accept' => 'application/json');
    $options = array('timeout' => $timeout) ;
    try{
        $resp = requests::get($url, $headers, $options);
        if ($resp->status_code != 200 ){
            $this->log(json_encode($resp,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            return "NA(resp->status_code): $resp->status_code != 200";
        }
    }catch (Exception $e) {
        return "Exception: " . $e->getMessage();
    }

    $body = Json_decode($resp->body, true);
    if ($body['code'] != 0 ) {
        $bodycode = $body['code'];
        return "NA(bodycode): $bodycode != 0 ";
    }

    //  所有客户数据
    $purchaserRows = $this->dao->select("*")->from($TABLE_PURCHASER)->fetchAll();
    foreach($purchaserRows as $row) {
        $purchaserNows["$row->code"] = $row;
        $purchaserNows["$row->name"] = $row;
    }
    // $this->log(json_encode(array_keys($purchaserNows),JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

    $bodyData = $body['data'];
    // $this->log(json_encode($bodyData[0],JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
    $cnt_ins = 0;
    $cnt_upd = 0;
    $cnt_del = 0;
    $cnt_same = 0;
    foreach($bodyData as $data) {

        $purchaser  = new stdclass();
        $purchaser->code = $data['companyId'];
        $purchaser->name = $data['customerName'];
        if ( isset($data['scoreNum']) ) $purchaser->scoreNum = $data['scoreNum'] ;  //行为分
        $purchaser->category = $data['type'];
        $purchaser->category0 = $data['type'];

        $code_pinyin = $this->pinyin($purchaser->name);
        if ( empty($purchaser->code) ) {
            $purchaser->code = $code_pinyin;
            // $this->log("to Pinyin: " . json_encode($purchaser,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        }else{
            // 去除前面的0
            $purchaser->code = '' . ($purchaser->code+0) ;
        }

        if ( $purchaser->category == "普通商家" ) {
            $purchaser->category = "SMB";
        }else if ( strpos(strtoupper($purchaser->category), 'B500') !== false ) {
            $purchaser->category = "B500";
        }else if ( strpos(strtoupper($purchaser->category), 'B5') !== false ) {
            $purchaser->category = "B5";
        }else if ( strpos(strtoupper($purchaser->category), 'B100') !== false ) {
            $purchaser->category = "B100";
        }else if ( strpos(strtoupper($purchaser->category), 'LKA') !== false ) {
            $purchaser->category = "LKA";
        }elseif ( strpos(strtoupper($purchaser->category), 'SMB') !== false ) {
            $purchaser->category = "SMB";
        }elseif ($purchaser->category == "B5商家" || $purchaser->category == "B5" ) {
            $purchaser->category = "B5";
        }elseif ($purchaser->category == "B100商家" || $purchaser->category == "B100" ) {
            $purchaser->category = "B100";
        }elseif ($purchaser->category == "B500商家"  || $purchaser->category == "B500") {
            $purchaser->category = "B500";
        }elseif ($purchaser->category == "LKA商家"  || $purchaser->category == "LKA") {
            $purchaser->category = "LKA";
        }elseif ($purchaser->category == "SMB商家"  || $purchaser->category == "SMB") {
            $purchaser->category = "SMB";
        }else {
            $purchaser->category = "SMB";
        }

        // $purchaserNow = $this->dao->select("*")->from($TABLE_PURCHASER)
        //     ->where("code")->eq($purchaser->code)
        //     ->orWhere("code")->eq($code_pinyin)
        //     ->fetch();
        $purchasersExist = array();
        $purchaserNow = null;
        if (array_key_exists($purchaser->code,$purchaserNows)) {
            $purchaserNow = $purchaserNows[$purchaser->code];
            $purchasersExist["{$purchaser->code}"] = $purchaserNow;
        }
        if (array_key_exists($code_pinyin,$purchaserNows)) {
            $purchaserNow = $purchaserNows[$code_pinyin];
            $purchasersExist["{$code_pinyin}"] = $purchaserNow;
        }
        if (array_key_exists($purchaser->name,$purchaserNows)) {
            $purchaserNow = $purchaserNows[$purchaser->name];
            $purchasersExist["{$purchaser->name}"] = $purchaserNow;
        }
        $purchaserNow = null;
        foreach($purchasersExist as $key => $value){
            if (empty($purchaserNow)) {
                $purchaserNow = $value;
            }elseif($value->code != $purchaserNow->code){ //  同name不同code
                $this->log("exist({$purchaserNow->code},{$purchaserNow->name}), DEL:" . json_encode(array("key"=>$key,"value"=>$value),JSON_UNESCAPED_UNICODE) , __FILE__, __LINE__);
                $this->dao->delete()->from($TABLE_PURCHASER)->where('code')->eq($value->code)->exec();
                $cnt_del ++;
            }
        }
        $this->log(json_encode(array("purchaserNow"=>$purchaserNow,"purchasersExist"=>$purchasersExist),JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        
        // $this->log(json_encode($purchaser,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        // $this->log(json_encode($purchaserNow,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);

        if (empty($purchaserNow)){
            $this->log("INS:" . json_encode($purchaser,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            $this->dao->insert($TABLE_PURCHASER)->data($purchaser,"category0")->exec();
            $cnt_ins = $cnt_ins + 1;
        }elseif ( $purchaser->code != $purchaserNow->code || $purchaser->name != $purchaserNow->name 
            || $purchaser->category != $purchaserNow->category || $purchaser->scoreNum != $purchaserNow->scoreNum ) {
            $this->log("UPD($purchaser->code,$code_pinyin):" . json_encode($purchaserNow,JSON_UNESCAPED_UNICODE) ." => ". json_encode($purchaser,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            $this->dao->update($TABLE_PURCHASER)->data($purchaser,"category0")->where('code')->eq($purchaserNow->code)->exec();
            $cnt_upd = $cnt_upd + 1;
        }else{
            $cnt_same = $cnt_same + 1;
        }
        if( dao::isError() )
        {
            $this->log("Fail to insert or update $TABLE_PURCHASER :" . json_encode($purchaser,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        }
        
    }
    $str = "cnt_ins = $cnt_ins, cnt_del = $cnt_del, cnt_upd = $cnt_upd, cnt_same = $cnt_same";
    $this->log($str, __FILE__, __LINE__);

    return $str ;
}
