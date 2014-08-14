<?php

class Magestore_Affiliatepluspayment_Model_Source_Period
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(){
        return array(
			array('value' => '7', 'label'=>Mage::helper('affiliateplus')->__('Weekly')),
			array('value' => '30', 'label'=>Mage::helper('affiliateplus')->__('Monthly')),
			array('value' => '365', 'label'=>Mage::helper('affiliateplus')->__('Yearly')),
			array('value' => '0', 'label'=>Mage::helper('affiliateplus')->__('Custom Period')),
        );
    }
}