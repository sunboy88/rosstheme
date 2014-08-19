<?php
class Magestore_Affiliatepluspayment_Block_Offline_Info extends Magestore_Affiliateplus_Block_Payment_Info
{
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate('affiliatepluspayment/offline/info.phtml');
		return $this;
    }
    
    public function getInvoiceAddress(){
        if(!$this->hasData('invoice_address')){
            $payment = $this->getPaymentMethod();
            $account = Mage::getSingleton('affiliateplus/session')->getAccount();
            $addressId = $payment->getAccountAddressId() ? $payment->getAccountAddressId() : $payment->getAddressId();
            $addressId = $addressId ? $addressId : 0 ;
            $verify = Mage::getModel('affiliateplus/payment_verify')->loadExist($account->getId(), $addressId, 'offline');
            $this->setData('invoice_address',$verify->getInfo());
        }
        return $this->getData('invoice_address');
    }
}