<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Model_Mysql4_Deal_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
  public function _construct(){
    parent::_construct();
    $this->_init('dailydeal/deal');
  }

  public function filterProductByCurrentStore() {
    $table_product_store = Mage::getSingleton('core/resource')->getTableName('catalog_category_product_index');
    $this->getSelect()->joinRight(
      array('ccpi' => $table_product_store),
      'main_table.product_id = ccpi.product_id'
      );
    $this->addFieldToFilter('main_table.deal_id', array('neq' => null));
    $store_id = Mage::app()->getStore()->getId();
    $this->addFieldToFilter('ccpi.store_id', array('in' => array('0',$store_id)));
    $this->getSelect()->group('main_table.deal_id');
    $this->getSelect()->distinct(true);
    return $this;
  }
}