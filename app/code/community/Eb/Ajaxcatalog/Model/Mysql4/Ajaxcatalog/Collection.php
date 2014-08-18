<?php

class Eb_Ajaxcatalog_Model_Mysql4_Ajaxcatalog_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ajaxcatalog/ajaxcatalog');
    }
}