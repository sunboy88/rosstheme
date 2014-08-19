<?php

class Magestore_Onestepcheckout_Model_Mysql4_Survey extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('onestepcheckout/survey', 'survey_id');
    }
}