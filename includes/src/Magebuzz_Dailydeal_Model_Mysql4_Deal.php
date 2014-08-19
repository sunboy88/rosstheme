<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Model_Mysql4_Deal extends Mage_Core_Model_Mysql4_Abstract
{
  public function _construct(){
    $this->_init('dailydeal/deal', 'deal_id');
  }
  
  protected function _afterLoad(Mage_Core_Model_Abstract $object) {
    if ($object->getId()) {
        $stores = $this->lookupStoreIds($object->getId());
        
        $object->setData('store_id', $stores);

    }
    return parent::_afterLoad($object);
  }

//  protected  function _beforeSave(Mage_Core_Model_Abstract $object) {
//    $deal_stores = $object->getStores();
//    $product_ids = $object->getDealProductIds();
//    $product_stores = Mage::getModel('catalog/product')->load($product_ids[0])->getWebsiteIds();
//
//    // if product_store has All Store value - 0
//    if (in_array(0,$product_stores)) {
//      return parent::_beforeSave($object);
//    }
//
//    if (count(array_diff($product_stores,$deal_stores))) {
//      Mage::getSingleton('core/session')->addError(Mage::helper('dailydeal')->__('Please select another Store View for Deal'));
//      return;
//    }
//    return parent::_beforeSave($object);
//  }
  
  protected function _afterSave(Mage_Core_Model_Abstract $object) {
    $oldStores = $this->lookupStoreIds($object->getId());
    $newStores = (array)$object->getStores();
    if (empty($newStores)) {
      $newStores = (array)$object->getStoreId();
    }
    $deal_store_table  = $this->getTable('dailydeal_deal_store');
    $insert = array_diff($newStores, $oldStores);
  $delete = array_diff($oldStores, $newStores);
    
    if ($delete) {
      $where = array(
          'deal_id = ?'     => (int) $object->getId(),
          'store_id IN (?)' => $delete
      );

      $this->_getWriteAdapter()->delete($deal_store_table, $where);
    }
    if ($insert) {
      $data = array();
      foreach ($insert as $storeId) {
          $data[] = array(
              'deal_id'  => (int) $object->getId(),
              'store_id' => (int) $storeId
          );
      }
      $this->_getWriteAdapter()->insertMultiple($deal_store_table, $data);
    }
    
    return parent::_afterSave($object);
  }
  
  public function lookupStoreIds($dealId) {
    $adapter = $this->_getReadAdapter();

    $select  = $adapter->select()
        ->from($this->getTable('dailydeal_deal_store'), 'store_id')
        ->where('deal_id = ?',(int)$dealId);

    return $adapter->fetchCol($select);
  }
}