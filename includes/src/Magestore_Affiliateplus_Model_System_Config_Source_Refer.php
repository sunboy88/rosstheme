<?php
/* add by VietBQ */
class Magestore_Affiliateplus_Model_System_Config_Source_Refer {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'email', 'label' => Mage::helper('affiliateplus')->__('Email')),
            array('value' => 'facebook', 'label' => Mage::helper('affiliateplus')->__('Facebook')),
            array('value' => 'twitter', 'label' => Mage::helper('affiliateplus')->__('Twitter')),
            array('value' => 'google', 'label' => Mage::helper('affiliateplus')->__('Google')),
        );
    }

}