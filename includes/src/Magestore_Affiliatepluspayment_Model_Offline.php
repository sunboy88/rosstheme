<?php

class Magestore_Affiliatepluspayment_Model_Offline extends Magestore_Affiliateplus_Model_Payment_Abstract
{
	protected $_code = 'offline';
	
	protected $_formBlockType = 'affiliatepluspayment/offline_form';
	protected $_infoBlockType = 'affiliatepluspayment/offline_info';
	
    
    /*event*/
    
    protected $_eventPrefix = 'affiliatepluspayment_offline';
    protected $_eventObject = 'affiliatepluspayment_offline';
    
    public function _construct(){
        parent::_construct();
        $this->_init('affiliatepluspayment/offline');
    }
    
    public function savePaymentMethodInfo(){
    	$payment = $this->getPayment();
    	if ($this->getOfflineAddressId()){
    		$address = Mage::getModel('customer/address')->load($this->getOfflineAddressId());
    		$this->setAddressId($address->getId())
	    		->setAddressHtml($address->format('html'));
    	}
    	$this->setTransferInfo($this->getOfflineTransferInfo())
    		->setMessage($this->getOfflineMessage());
    	$this->setPaymentId($payment->getId())->save();
		return parent::savePaymentMethodInfo();
    }
    
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
    
    public function calculateFee(){
    	return $this->getPayment()->getFee();
    }
    
    public function getInfoString(){
		return Mage::helper('affiliateplus/payment')->__('
			Method: %s \n
		',$this->getLabel());
	}
	
	public function getInfoHtml(){
		$html = Mage::helper('affiliateplus/payment')->__('Method: ');
		$html .= '<strong>'.$this->getLabel().'</strong><br />';
		return $html;
	}
    
    protected function _afterSave(){
        $payment = $this->getPayment();
        if($payment->getStatus()== 3){
            if($payment->getPaymentMethod() == 'offline'){
                $verify = Mage::getModel('affiliateplus/payment_verify')->loadExist($payment->getAccountId(), $this->getAddressId(), 'offline');
                if(!$verify->isVerified()){
                    try{
                    $verify->setVerified(1)
                            ->save();
                    }  catch (Exception $e){
                        
                    }
                }
            }
        }elseif ($payment->getStatus() == 1) {
            if($payment->getPaymentMethod() == 'offline'){
                $verify = Mage::getModel('affiliateplus/payment_verify')->loadExist($payment->getAccountId(), 0, 'offline');
                if($verify->getId()){
                    try{
                        $verify->setData('field',$this->getAddressId())
                            ->save();
                        
                    }  catch (Exception $e){
                        Zend_Debug::dump($e->getMessage());
                    }
                }
            }
        }
    }
}