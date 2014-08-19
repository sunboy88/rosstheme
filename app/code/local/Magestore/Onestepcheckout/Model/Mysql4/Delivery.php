<?php

class Magestore_Onestepcheckout_Model_Mysql4_Delivery extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('onestepcheckout/delivery', 'delivery_id');
    }
}