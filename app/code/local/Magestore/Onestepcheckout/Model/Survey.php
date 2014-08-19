<?php
class Magestore_Onestepcheckout_Model_Survey extends Mage_Core_Model_Abstract 
{	
	public function _construct()	
	{
		parent::_construct();
		$this->_init('onestepcheckout/survey');
	}	
}