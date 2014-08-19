<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_View extends Mage_Core_Block_Template {
  public function _construct() {
    return parent::_construct();
  }

  public function getDeals() {
    return Mage::helper('dailydeal')->getTodayDeals();
  }

  public function displayOnLeftSidebarBlock() {
    $block = $this->getParentBlock();
    if ($block) {
      if (Mage::getStoreConfig('dailydeal/general/show_on_sidebar')=='left') {          
        $sidebarBlock = $this->getLayout()->createBlock('dailydeal/sidebar');     
        $block->insert($sidebarBlock, '', true, 'deal-sidebar');
      }
    }
  }

  public function displayOnRightSidebarBlock() {
    $block = $this->getParentBlock();
    if ($block) {
      if (Mage::getStoreConfig('dailydeal/general/show_on_sidebar')=='right') {
        $sidebarBlock = $this->getLayout()->createBlock('dailydeal/sidebar');
        $block->insert($sidebarBlock, '', true, 'deal-sidebar');
      }
    }
  }
}
