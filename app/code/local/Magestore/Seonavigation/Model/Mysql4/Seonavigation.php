<?php

class Magestore_Seonavigation_Model_Mysql4_Seonavigation extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('seonavigation/seonavigation', 'seonavigation_id');
	}
}