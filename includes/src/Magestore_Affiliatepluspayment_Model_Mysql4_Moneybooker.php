<?php

class Magestore_Affiliatepluspayment_Model_Mysql4_Moneybooker extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
    {
        $this->_init('affiliatepluspayment/moneybooker', 'payment_moneybooker_id');
    }
}