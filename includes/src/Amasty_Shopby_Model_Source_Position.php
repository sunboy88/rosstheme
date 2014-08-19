<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Source_Position extends Amasty_Shopby_Model_Source_Abstract
{
    const LEFT = 'left';
    const TOP = 'top';
    const BOTH = 'both';

    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
            array('value' => self::LEFT, 'label' => $hlp->__('Sidebar')),
            array('value' => self::TOP,  'label' => $hlp->__('Top')),
            array('value' => self::BOTH, 'label' => $hlp->__('Both')),
        );
    }
    
}