<?php
/**
 * @copyright  Copyright (c) 2010 Amasty (http://www.amasty.com)
 */  
class Amasty_Shopby_Model_Source_Attribute extends Amasty_Shopby_Model_Source_Abstract
{
    const DT_LABELS_ONLY = 0;
    const DT_IMAGES_ONLY = 1;
    const DT_IMAGES_AND_LABELS = 2;
    const DT_DROPDOWN = 3;
    const DT_LABELS_IN_2_COLUMNS = 4;

    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
            array('value' => self::DT_LABELS_ONLY, 'label' => $hlp->__('Labels Only')),
            array('value' => self::DT_IMAGES_ONLY, 'label' => $hlp->__('Images Only')),
            array('value' => self::DT_IMAGES_AND_LABELS, 'label' => $hlp->__('Images and Labels')),
            array('value' => self::DT_DROPDOWN, 'label' => $hlp->__('Drop-down List')),
            array('value' => self::DT_LABELS_IN_2_COLUMNS, 'label' => $hlp->__('Labels in 2 columns')),
        );
    }
}