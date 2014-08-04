<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Source_Slider extends Varien_Object
{
    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
            array('value' => 0, 'label' => $hlp->__('Default')),
            array('value' => 1, 'label' => $hlp->__('With ranges')),
        );
    }
    
}