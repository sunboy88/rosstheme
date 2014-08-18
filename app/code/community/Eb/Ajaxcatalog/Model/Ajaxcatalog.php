<?php

class Eb_Ajaxcatalog_Model_Ajaxcatalog extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ajaxcatalog/ajaxcatalog');
    }
}