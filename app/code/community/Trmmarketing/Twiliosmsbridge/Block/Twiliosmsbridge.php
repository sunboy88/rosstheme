<?php
class Trmmarketing_Twiliosmsbridge_Block_Twiliosmsbridge extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getTwiliosmsbridge()     
     { 
        if (!$this->hasData('twiliosmsbridge')) {
            $this->setData('twiliosmsbridge', Mage::registry('twiliosmsbridge'));
        }
        return $this->getData('twiliosmsbridge');
        
    }
}