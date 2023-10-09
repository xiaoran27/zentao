<?php

public function syncStarlink($timeout=30, $minutes=5)
{
    $maxGapMinute = $minutes;

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

    $diff = $this->dao->select("timestampdiff(second , max(mtime),now()) as diff")->from($TABLE_PURCHASER)->fetch("diff");
    $this->log("diff=$diff", __FILE__, __LINE__);
    if ( $diff  < $maxGapMinute * 60 - 30 ) {   //  允许误差30s 
        return "NA(seconds): $diff  < $maxGapMinute * 60 - 30";
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
        if ( !isset($data['scoreNum']) || empty($data['scoreNum']) )  $data['scoreNum'] = 0;
        $purchaser->scoreNum = $data['scoreNum'] ;  //行为分
        $purchaser->category = $data['type'];
        $purchaser->category0 = $data['type'];

        if ( empty($purchaser->code) ) {
            $purchaser->code = "0";
        }else{
            // 去除前面的0
            $purchaser->code = '' . ($purchaser->code+0) ;
        }

        if ( $purchaser->category == "普通商家" ) {
            $purchaser->category = "SMB";
        // }else if ( strpos(strtoupper($purchaser->category), 'B500') !== false ) {
        //     $purchaser->category = "B500";
        // }else if ( strpos(strtoupper($purchaser->category), 'B5') !== false ) {
        //     $purchaser->category = "B5";
        // }else if ( strpos(strtoupper($purchaser->category), 'B100') !== false ) {
        //     $purchaser->category = "B100";
        // }else if ( strpos(strtoupper($purchaser->category), 'LKA') !== false ) {
        //     $purchaser->category = "LKA";
        // }elseif ( strpos(strtoupper($purchaser->category), 'SMB') !== false ) {
        //     $purchaser->category = "SMB";
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
            // $purchaser->category = "SMB";
        }
        

        // code name  增删改
        $purchaserNow = null;
        if ($purchaser->code != "0" && array_key_exists($purchaser->code,$purchaserNows)) {  // 班牛ID相同
            $purchaserNow = $purchaserNows[$purchaser->code];
            $purchaserNow->eq = 'code';
            $purchasersExist["{$purchaser->code}"] = $purchaserNow;

            if ( $purchaser->code == $purchaserNow->code && $purchaser->name == $purchaserNow->name 
                && $purchaser->category == $purchaserNow->category && $purchaser->scoreNum == $purchaserNow->scoreNum ){
                $cnt_same = $cnt_same + 1;
                continue;  
            }

            // code同,name不同,
            if ( $purchaser->name != $purchaserNow->name && array_key_exists($purchaser->name,$purchaserNows)) {

                
                $this->dao->delete()->from($TABLE_PURCHASER)->where('name')->eq($purchaser->name)->exec();
                $cnt_del = $cnt_del + 1;

                unset($purchaserNows["$purchaserNow->name"]);
                $purchaserNow2 = $purchaserNows[$purchaser->name];
                unset($purchaserNows["$purchaserNow2->code"]);
                unset($purchaserNows["$purchaserNow2->name"]);
                
            }

            // 班牛ID相同, 其他有不同
            $purchaser->modifier = 'syncStarlink';
            $this->log("UPD:" . json_encode($purchaserNow, JSON_UNESCAPED_UNICODE) ." => ". json_encode($purchaser,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            $this->dao->update($TABLE_PURCHASER)->data($purchaser,"category0")->where('code')->eq($purchaserNow->code)->exec();
            if( dao::isError() )
            {
                $this->log("Fail to UPD $TABLE_PURCHASER :" . json_encode($purchaser,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            }else{
                unset($purchaserNows["$purchaserNow->name"]);
                $purchaserNows["$purchaser->code"] = $purchaser;
                $purchaserNows["$purchaser->name"] = $purchaser;

                $cnt_upd = $cnt_upd + 1;
                if ($purchaserNow->code != $purchaser->code) $this->updateRefPurchaser($purchaserNow->code, $purchaser->code);
            }
            continue;
        }

        // $code_pinyin = $this->pinyin($purchaser->name);  // 仅首字母
        // $code_pinyin_full = $this->pinyin($purchaser->name, true);  //  全拼
        if (array_key_exists($purchaser->name,$purchaserNows)) {  // 名称相同，无班牛ID
            $purchaserNow = $purchaserNows[$purchaser->name];
            $purchaserNow->eq = 'name';
            $purchasersExist["{$purchaser->name}"] = $purchaserNow;

            if ( $purchaser->code == "0" && $purchaser->name == $purchaserNow->code && $purchaser->name == $purchaserNow->name 
            // if ( $purchaser->code == "0" && ( $code_pinyin == $purchaserNow->code || $code_pinyin_full == $purchaserNow->code ) && $purchaser->name == $purchaserNow->name 
                && $purchaser->category == $purchaserNow->category && $purchaser->scoreNum == $purchaserNow->scoreNum ){
                $cnt_same = $cnt_same + 1;
                continue;  
            }

            // 名称相同, 其他有不同
            $purchaser->modifier = 'syncStarlink';
            $purchaser->code = $purchaser->code == "0" ? $purchaserNow->code : $purchaser->code;
            $this->log("UPD:" . json_encode($purchaserNow, JSON_UNESCAPED_UNICODE) ." => ". json_encode($purchaser,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            $this->dao->update($TABLE_PURCHASER)->data($purchaser,"category0")->where('name')->eq($purchaser->name)->exec();
            if( dao::isError() )
            {
                $this->log("Fail to UPD $TABLE_PURCHASER :" . json_encode($purchaser,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
            }else{
                unset($purchaserNows["$purchaserNow->code"]);
                $purchaserNows["$purchaser->code"] = $purchaser;
                $purchaserNows["$purchaser->name"] = $purchaser;

                $cnt_upd = $cnt_upd + 1;
                if ($purchaserNow->code != $purchaser->code) $this->updateRefPurchaser($purchaserNow->code, $purchaser->code);
            }
            continue;

        }

        // // Deprecated
        // if (array_key_exists($code_pinyin,$purchaserNows)) {  // pinyin相同，名称不同
        //     $purchaserNow = $purchaserNows[$code_pinyin];
        //     $purchaserNow->eq = 'pinyin';
        //     $purchasersExist["{$code_pinyin}"] = $purchaserNow;
        // }
        // if (array_key_exists($code_pinyin_full,$purchaserNows)) {  // pinyin_full相同，名称不同
        //     $purchaserNow = $purchaserNows[$code_pinyin_full];
        //     $purchaserNow->eq = 'pinyin_full';
        //     $purchasersExist["{$code_pinyin_full}"] = $purchaserNow;
        // }
        
        
        $purchaser->creator = 'syncStarlink';
        $purchaser->modifier = 'syncStarlink';
        // $purchaser->code = $purchaser->code == "0" ? ( array_key_exists($code_pinyin,$purchaserNows) ? $code_pinyin_full : $code_pinyin ) : $purchaser->code;
        $purchaser->code = $purchaser->code == "0" ? $purchaser->name : $purchaser->code;
        $this->log("INS:" . json_encode($purchaser,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        $this->dao->insert($TABLE_PURCHASER)->data($purchaser,"category0")->exec();
        if( dao::isError() )
        {
            $this->log("Fail to INS $TABLE_PURCHASER :" . json_encode($purchaser,JSON_UNESCAPED_UNICODE), __FILE__, __LINE__);
        }else{
            $purchaserNows["$purchaser->code"] = $purchaser;
            $purchaserNows["$purchaser->name"] = $purchaser;

            $cnt_ins = $cnt_ins + 1;
        }
        
    }
    $str = "cnt_ins = $cnt_ins, cnt_del = $cnt_del, cnt_upd = $cnt_upd, cnt_same = $cnt_same";
    $this->log($str, __FILE__, __LINE__);

    return $str ;
}
