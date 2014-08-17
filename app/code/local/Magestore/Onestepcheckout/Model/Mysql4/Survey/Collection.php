<?php
class Magestore_Onestepcheckout_Model_Mysql4_Survey_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {	
        parent::_construct();
        $this->_init('onestepcheckout/survey');
    }
}