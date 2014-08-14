<?php

class Magestore_Affiliatepluswidget_Model_Mysql4_Widget_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct(){
        parent::_construct();
        $this->_init('affiliatepluswidget/widget');
    }
}