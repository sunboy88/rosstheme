<?php

class Magestore_Affiliatepluswidget_Model_Mysql4_Widget extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct(){
        $this->_init('affiliatepluswidget/widget', 'widget_id');
    }
}