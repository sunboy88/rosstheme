<?php

class Magestore_Affiliatepluswidget_Model_Widget extends Mage_Core_Model_Abstract
{
    public function _construct(){
        parent::_construct();
        $this->_init('affiliatepluswidget/widget');
    }
    
    protected function _beforeSave(){
    	if (is_array($this->getCategoryIds()))
    		$this->setCategoryIds(implode(',',$this->getCategoryIds()));
    	return parent::_beforeSave();
    }
    
    protected function _afterLoad(){
    	if (is_string($this->getCategoryIds()))
    		$this->setCategoryIds(explode(',',$this->getCategoryIds()));
    	return parent::_afterLoad();
    }
    
    public function getWidgetCode(){
    	return Mage::helper('affiliatepluswidget')->getWidgetCode($this->getData());
    }
}