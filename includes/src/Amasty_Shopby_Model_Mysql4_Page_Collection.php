<?php
/**
 * @author Amasty
 */ 
class Amasty_Shopby_Model_Mysql4_Page_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('amshopby/page');
        $this->setOrder('num', 'desc');
    }

    public function addStoreFilter()
    {
        $this->getSelect()->where('store_id = 0 OR store_id = ' . Mage::app()->getStore()->getId());
    }
}