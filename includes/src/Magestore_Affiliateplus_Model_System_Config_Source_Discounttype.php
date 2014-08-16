<?php

class Magestore_Affiliateplus_Model_System_Config_Source_Discounttype
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'fixed', 'label'=>Mage::helper('affiliateplus')->__('Fixed Amount per Item')),
            array('value' => 'percentage', 'label'=>Mage::helper('affiliateplus')->__('Percentage')),
            array('value' => 'cart_fixed', 'label'=>Mage::helper('affiliateplus')->__('Fixed Amount per Cart')),
        );
    }
}
