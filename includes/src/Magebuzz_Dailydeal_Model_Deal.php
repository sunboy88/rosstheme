<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Model_Deal extends Mage_Core_Model_Abstract
{
  public function _construct(){
    parent::_construct();
    $this->_init('dailydeal/deal');
  }
  
  public function getProduct(){
    $productId = $this->getProductId();
    $product = Mage::getModel('catalog/product')->load($productId);
    return $product;
  }
  
  public function loadByProductId($productId){
    $dealCollection = $this->getCollection()
			->addFieldToFilter('product_id',$productId);
    if ($dealCollection->getSize()) {
      $this->load($dealCollection->getFirstItem()->getId());
    }
    return $this;
  }
  
  public function updateStatus() {
    $now_time = Mage::getModel('core/date')->gmtTimestamp();
    if(!($this->getStatus() == '4')){
      if(strtotime($this->getStartTime()) > $now_time){
        $status = 1;
      }else if((strtotime($this->getStartTime()) <= $now_time) && ($now_time < strtotime($this->getEndTime()))){
        $status = 2;
      }else{
        $status = 3;
      }
      if ($this->getProductId()) {
        $query = "UPDATE ".$this->_getTablename('dailydeal_deal')." SET `status`='" . $status . "' WHERE `deal_id`= ".$this->getId();
        
        $this->_getWriteConnection()->query($query);
      }
    }
  }
  
  
  protected function _getReadConnection() {
    return Mage::getSingleton('core/resource')->getConnection('core_read');
  }
  
  protected function _getWriteConnection() {
    return Mage::getSingleton('core/resource')->getConnection('core_write');
  }
  
  protected function _getTableName($name) {
    return Mage::getSingleton('core/resource')->getTableName($name);
  }
}