<?php
class Magestore_Affiliatepluspayment_Block_Confirm extends Mage_Core_Block_Template
{
    public function getAccount(){
    	return Mage::getSingleton('affiliateplus/session')->getAccount();
    }
    
    /**
     * get Payment Model
     *
     * @return Magestore_Affiliateplus_Model_Payment
     */
    public function getPayment(){
    	if (!$this->hasData('payment')){
    		$payment = Mage::registry('confirm_payment_data');
    		$payment->addPaymentInfo();
    		$this->setData('payment',$payment);
    	}
    	return $this->getData('payment');
    }
    
    /**
     * get Payment Method
     *
     * @return Magestore_Affiliateplus_Model_Payment_Abstract
     */
    public function getPaymentMethod(){
    	return $this->getPayment()->getPayment();
    }
    
    public function getStatusArray(){
    	return array(
			1	=> $this->__('Pending'),
			2	=> $this->__('Processing'),
			3	=> $this->__('Completed'),
		);
    }
    
    public function _prepareLayout(){
		parent::_prepareLayout();
		
		if ($this->getPaymentMethod())
		if ($paymentMethodInfoBlock = $this->getLayout()->createBlock($this->getPaymentMethod()->getInfoBlockType(),'payment_method_info')){
			$paymentMethodInfoBlock->setPaymentMethod($this->getPaymentMethod());
			$this->setChild('payment_method_info',$paymentMethodInfoBlock);
		}
		
		return $this;
    }
}