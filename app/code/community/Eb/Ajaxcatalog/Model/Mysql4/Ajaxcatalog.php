<?php

class Eb_Ajaxcatalog_Model_Mysql4_Ajaxcatalog extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the ajaxcatalog_id refers to the key field in your database table.
        $this->_init('ajaxcatalog/ajaxcatalog', 'ajaxcatalog_id');
    }
}