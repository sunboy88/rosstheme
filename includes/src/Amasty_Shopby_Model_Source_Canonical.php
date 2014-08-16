<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Source_Canonical extends Varien_Object
{
    const CANONICAL_KEY = 0;
    const CANONICAL_CURRENT_URL = 1;
    const CANONICAL_FIRST_ATTRIBUTE_VALUE = 2;

    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
            array('value' => self::CANONICAL_KEY, 'label' => $hlp->__('Just Url Key')),
            array('value' => self::CANONICAL_CURRENT_URL, 'label' => $hlp->__('Current URL')),
            array('value' => self::CANONICAL_FIRST_ATTRIBUTE_VALUE, 'label' => $hlp->__('First Attribute Value')),
        );
    }
}