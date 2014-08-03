<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Source_Specialchar extends Varien_Object
{
    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
        	array('value' => '_', 'label' => $hlp->__('_')),
            array('value' => '-', 'label' => $hlp->__('-')),
        );
    }
    
}