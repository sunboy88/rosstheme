<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_PaypalBNCode
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * PaypalBNCode Model
 * 
 * @category 	Magestore
 * @package 	Magestore_PaypalBNCode
 * @author  	Magestore Developer
 */
class Magestore_PaypalBNCode_Model_Config extends Mage_Paypal_Model_Config
{
    /**
     * BN code getter
     * override method
     *
     * @param string $countryCode ISO 3166-1
     */
    public function getBuildNotationCode($countryCode = null)
    {
		if($this->isMageEnterprise()){
			$newBnCode = 'Magestore_SI_MagentoEE';
		} else {
			$newBnCode = 'Magestore_SI_MagentoCE';
		}
		
        $bnCode = parent::getBuildNotationCode($countryCode);
		
		if(class_exists("Magestore_Onestepcheckout_Helper_Data") && Mage::getStoreConfig('onestepcheckout/general/active')){
			return $newBnCode;
		} else {
			return $bnCode;
		}
    }
	
	public function isMageEnterprise() {
		return Mage::getConfig ()->getModuleConfig ( 'Enterprise_Enterprise' ) 
			&& Mage::getConfig ()->getModuleConfig ( 'Enterprise_AdminGws' ) 
			&& Mage::getConfig ()->getModuleConfig ( 'Enterprise_Checkout' ) 
			&& Mage::getConfig ()->getModuleConfig ( 'Enterprise_Customer' );
	}		

}