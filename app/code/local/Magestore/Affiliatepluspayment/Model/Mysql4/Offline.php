<?php

class Magestore_Affiliatepluspayment_Model_Mysql4_Offline extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct(){
        $this->_init('affiliatepluspayment/offline', 'id');
    }
}