<?php
/**
* @copyright Amasty.
*/  
class Amasty_Shopby_Model_Mysql4_Value extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amshopby/value', 'value_id');
    }
}