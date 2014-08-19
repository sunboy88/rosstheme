<?php
class Magestore_Affiliatepluspayment_Block_Bank_Info extends Magestore_Affiliateplus_Block_Payment_Info
{
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate('affiliatepluspayment/bank/info.phtml');
		return $this;
    }
    
    public function getBankStatement(){
        if(!$this->hasData('bank_statement')){
            $payment = $this->getPaymentMethod();
            $account = Mage::getSingleton('affiliateplus/session')->getAccount();
            $bankaccountId = $payment->getPaymentBankaccountId() ? $payment->getPaymentBankaccountId() : $payment->getBankaccountId();
            $bankaccountId = $bankaccountId ? $bankaccountId : 0;
            $verify = Mage::getModel('affiliateplus/payment_verify')->loadExist($account->getId(), $bankaccountId, 'bank');            
            
            $this->setData('bank_statement',$verify->getInfo());
        }
        return $this->getData('bank_statement');
    }
}