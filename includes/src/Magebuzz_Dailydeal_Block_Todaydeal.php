<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Todaydeal extends Mage_Catalog_Block_Product_Abstract {

  public function _prepareLayout() {
    parent::_prepareLayout();
    $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
    $pager->setAvailableLimit($this->getPagingValues());
    $pager->setCollection(Mage::helper('dailydeal')->getTodayDeals());
    $this->setChild('pager', $pager);
    return $this;
  }
  
  public function getPagerHtml() {
    return $this->getChildHtml('pager');
  }
  
  public function getPagingValues() {
    $perPageConfigKey = 'dailydeal/general/deals_grid_per_page_values';
    $perPageValues = (string)Mage::getStoreConfig($perPageConfigKey);
    $perPageValues = explode(',', $perPageValues);
    $perPageValues = array_combine($perPageValues, $perPageValues);
    if (Mage::getStoreConfigFlag('dailydeal/general/show_all_deals')) {
      return ($perPageValues + array('all'=>$this->__('All')));
    }else {
      return $perPageValues;
    }
  }
}