<?php
class Magestore_Affiliatepluspayment_Block_Email_Admin extends Mage_Core_Block_Template
{
    public function getStatusLabels() {
        return array(
            1 =>  Mage::helper('affiliateplus')->__('Pending'),
            2 =>  Mage::helper('affiliateplus')->__('Processing'),
            3 =>  Mage::helper('affiliateplus')->__('Completed'),
            4 =>  Mage::helper('affiliateplus')->__('Canceled')
        );
    }
    
    public function getFormatBaseCurrency($amount) {
        // return Mage::helper('core')->currency($amount);
        $store = Mage::app()->getStore($this->getStore());
        return $store->convertPrice($amount, true);
    }
}
