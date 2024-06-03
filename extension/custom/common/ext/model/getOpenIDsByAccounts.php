<?php

/**
* Get bind account's openID list.
*
* @param  string   $type='ding'     ding|wechat|feishu
* @param  array   $accounts
* @access public
* @return Array()  accouont=>openID 
*/
public function getOpenIDsByAccounts($type='ding', $accounts = array())
{
   $data = $this->dao->select('distinct zo.account as account,zo.openID as openID')->from(TABLE_OAUTH)->alias("zo")
      ->leftJoin(TABLE_WEBHOOK)->alias("zw")->on("zo.providerID = zw.id")
      ->where('zw.type')->like($type.'%')
      ->andWhere('zo.providerType')->eq('webhook')
      ->beginIF($accounts)->andWhere('zo.account')->in($accounts)->fi()
      ->fetchAll();

   foreach ($data as $value) {
      $openIds["$value->account"] = $value->openID;
   }
   return $openIds;
}