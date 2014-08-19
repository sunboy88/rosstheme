<?php
/**
 * @author Amasty
 */ 
class Amasty_Shopby_Model_Mysql4_Range_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('amshopby/range');
    }
}