<?php

class Trmmarketing_Twiliosmsbridge_Model_Mysql4_Twiliosmsbridge_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('twiliosmsbridge/twiliosmsbridge');
    }
}