<?php

class Magestore_Seonavigation_Model_Mysql4_Seonavigation_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('seonavigation/seonavigation');
	}
}