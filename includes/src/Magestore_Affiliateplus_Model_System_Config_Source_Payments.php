<?php

class Magestore_Affiliateplus_Model_System_Config_Source_Payments
{
    /**
	 * Get Affiliate Payment Helper
	 *
	 * @return Magestore_Affiliateplus_Helper_Payment
	 */
	protected function _getPaymentHelper(){
		return Mage::helper('affiliateplus/payment');
	}

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $paymentMethods = array();
        $store = Mage::app()->getRequest()->getParam('store');
        $availableMethods = $this->_getPaymentHelper()->getAvailablePayment($store);
        foreach($availableMethods as $code => $method){
            $paymentMethods[$code] = $method->getLabel();
        }
        return $paymentMethods;
    }

}