<?php
include '../../../../../module/bug/control.php';

class myBug extends bug
{
    /**
     * Ajax get product bugs.
     *
     * @param  int     $productID
     * @param  int     $bugID
     * @access public
     * @return string
     */
    public function ajaxGetProductBugs($productID, $bugID)
    {
        $product     = $this->loadModel('product')->getById($productID);
        $bug         = $this->bug->getById($bugID);
        $branch      = $product->type == 'branch' ? ($bug->branch > 0 ? $bug->branch . ',0' : '0') : '';
        $productBugs = $this->bug->getProductBugPairs(null, $branch);
        unset($productBugs[$bugID]);

        return print(html::select('duplicateBug', $productBugs, '', "class='form-control' placeholder='{$this->lang->bug->duplicateTip}'"));
    }
}