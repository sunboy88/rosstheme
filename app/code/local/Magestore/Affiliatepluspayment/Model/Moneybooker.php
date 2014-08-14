<?php

class Magestore_Affiliatepluspayment_Model_Moneybooker extends Magestore_Affiliateplus_Model_Payment_Abstract
{
	protected $_code = 'moneybooker';
	
	protected $_formBlockType = 'affiliatepluspayment/moneybooker';
	
    public function _construct(){
        parent::_construct();
        $this->_init('affiliatepluspayment/moneybooker');
    }
	
    public function calculateFee(){
    	return $this->getPayment()->getFee();
    }
    
    public function getInfoString(){
		$info = Mage::helper('affiliateplus/payment')->__('
			Method: %s \n
			Email: %s \n'
			//Fee: %s \n
			// 'Transaction Id: %s \n
		,$this->getLabel()
		,$this->getEmail()
		//,$this->getFeePrice(false)
		//,$this->getTransactionId()
        );
        if ($this->getTransactionId()) {
            return $info . Mage::helper('affiliateplus/payment')->__('Transaction Id: %s \n', $this->getTransactionId());
        }
        return $info;
	}
	
	public function getInfoHtml(){
        if(!$this->getId()){
            $payment = Mage::registry('confirm_payment_data');
            if($payment)
                $this->setData($payment->getData());
        }
		$html = Mage::helper('affiliateplus/payment')->__('Method: ');
		$html .= '<strong>'.$this->getLabel().'</strong><br />';
		$html .= Mage::helper('affiliateplus/payment')->__('Email: ');
		$html .= '<strong>'.$this->getEmail().'</strong><br />';
		//$html .= Mage::helper('affiliateplus/payment')->__('Fee: ');
		//$html .= '<strong>'.$this->getFeePrice(true).'</strong><br />';
        if($this->getId() && $this->getTransactionId()){
            $html .= Mage::helper('affiliateplus/payment')->__('Transaction Id: ');
            $html .= '<strong>'.$this->getTransactionId().'</strong><br />';
        }
		return $html;
	}
	
	/**
	 * load information of moneybooker payment method
	 *
	 * @return Magestore_Affiliatepluspayment_Model_Moneybooker
	 */
	public function loadPaymentMethodInfo(){
		if ($this->getPayment()){
			$paymentInfo = $this->getCollection()
				->addFieldToFilter('payment_id',$this->getPayment()->getId())
				->getFirstItem();
			if ($paymentInfo)
				$this->addData($paymentInfo->getData())->setId($paymentInfo->getId());
		}
		return parent::loadPaymentMethodInfo();
	}
	
	/**
	 * Save Payment Method Information
	 *
	 * @return Magestore_Affiliateplus_Model_Payment_Abstract
	 */
	public function savePaymentMethodInfo(){
		$this->setPaymentId($this->getPayment()->getId())->save();
		return parent::savePaymentMethodInfo();
	}
        
        public function getEstimateFee ($amount, $payer){
            $amount = floatval($amount);
            $fee = 0;
            if($payer == 'recipient'){
                $fee = floatval($amount*0.01/1.01);
            }  else {
                $fee = $amount*0.01;
            }
            return $fee;
        }
        
        protected function _beforeSave(){
            if ($this->getData('moneybooker_email')) {
                $this->setData('email', $this->getData('moneybooker_email'));
            }
            if ($this->getData('moneybooker_transaction_id')) {
                $this->setData('transaction_id', $this->getData('moneybooker_transaction_id'));
            }
            return parent::_beforeSave();
        }
}