<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Product_Dailydeal extends Mage_Core_Block_Template {
  public function _prepareLayout() {
    return parent::_prepareLayout();
  }

  public function getDeal($product_id) {
    return Mage::helper('dailydeal')->getDealByProductId($product_id);
  }

  public function getDealUrl($deal_id) {
    return Mage::getUrl('dailydeal/deal/view', array('deal_id' => $deal_id));
  }

}