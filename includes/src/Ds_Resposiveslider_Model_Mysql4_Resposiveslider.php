<?php
class Ds_Resposiveslider_Model_Mysql4_Resposiveslider extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init("resposiveslider/resposiveslider", "slide_id");
    }
}