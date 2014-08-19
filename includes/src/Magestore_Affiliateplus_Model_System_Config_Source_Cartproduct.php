<?php

class Magestore_Affiliateplus_Model_System_Config_Source_Cartproduct
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'cart', 'label'=>Mage::helper('affiliateplus')->__('On shopping cart page')),
            array('value' => 'product', 'label'=>Mage::helper('affiliateplus')->__('On product pages')),
        );
    }

}