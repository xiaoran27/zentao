<?php


public function updateRefPurchaser($old, $new){

    if ($old == $new) return;

    $this->dbh->exec("UPDATE " . TABLE_STORY . " SET purchaser = replace(replace(replace(concat(' ,',purchaser,', '),',$old,',',$new,'),' ,',''),', ','') WHERE concat(',',purchaser,',') like '%,$old,%'");
    $this->dbh->exec("UPDATE " . TABLE_BUG . " SET purchaser = replace(replace(replace(concat(' ,',purchaser,', '),',$old,',',$new,'),' ,',''),', ','') WHERE concat(',',purchaser,',') like '%,$old,%'");

    // static $TABLE_PURCHASER = "zt_purchaser";
    // $this->dbh->exec("UPDATE " . $TABLE_PURCHASER . " SET code = '$new' WHERE code = '$old'");
    
    $this->log("updateRefPurchaser($old, $new) for (" . TABLE_STORY . "," . TABLE_BUG . ")", __FILE__, __LINE__);
}


