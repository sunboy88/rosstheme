<?php

class Magestore_Onestepcheckout_Model_Mysql4_Countryversion extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('onestepcheckout/countryversion', 'version_id');
    }
}