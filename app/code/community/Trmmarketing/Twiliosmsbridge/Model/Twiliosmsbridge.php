<?php

class Trmmarketing_Twiliosmsbridge_Model_Twiliosmsbridge extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('twiliosmsbridge/twiliosmsbridge');
    }
}