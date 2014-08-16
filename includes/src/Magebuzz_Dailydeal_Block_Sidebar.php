<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Sidebar extends Mage_Catalog_Block_Product_Abstract {
  public function _construct() {
    $this->setTemplate('dailydeal/sidebar.phtml');
    return parent::_construct();
  }
}