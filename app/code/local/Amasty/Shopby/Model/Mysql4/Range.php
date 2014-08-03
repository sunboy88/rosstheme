<?php
/**
 * @author Amasty
 */ 
class Amasty_Shopby_Model_Mysql4_Range extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amshopby/range', 'range_id');
    }
}