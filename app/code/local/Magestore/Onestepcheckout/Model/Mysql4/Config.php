<?php
class Magestore_Onestepcheckout_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('onestepcheckout/config', 'config_id');
    }
}
?>
