<?php
class Devinc_Occ_Block_Occ_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods
{	
    //allow only selected payment methods for occ
    protected function _canUseMethod($method)
    {
    	$allowedMethods = explode(',',Mage::getStoreConfig('occ/configuration/payment_methods'));
        if (!in_array($method->getCode(), $allowedMethods)) {
        	return false;
        }
        
        return parent::_canUseMethod($method);
    }
}