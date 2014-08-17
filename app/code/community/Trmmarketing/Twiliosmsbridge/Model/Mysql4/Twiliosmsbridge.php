<?php

class Trmmarketing_Twiliosmsbridge_Model_Mysql4_Twiliosmsbridge extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the twiliosmsbridge_id refers to the key field in your database table.
        $this->_init('twiliosmsbridge/twiliosmsbridge', 'twiliosmsbridge_id');
    }
}