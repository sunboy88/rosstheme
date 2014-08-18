<?php
class Eb_Ajaxcatalog_Block_Ajaxcatalog extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getAjaxcatalog()     
     { 
        if (!$this->hasData('ajaxcatalog')) {
            $this->setData('ajaxcatalog', Mage::registry('ajaxcatalog'));
        }
        return $this->getData('ajaxcatalog');
        
    }
}