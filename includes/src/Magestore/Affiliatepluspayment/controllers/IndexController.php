<?php
class Magestore_Affiliatepluspayment_IndexController extends Mage_Core_Controller_Front_Action
{
    
    /**
    * get Account helper
    *
    * @return Magestore_Affiliateplus_Helper_Account
    */
    protected function _getAccountHelper(){
            return Mage::helper('affiliateplus/account');
    }
    
    /**
    * get Core Session
    *
    * @return Mage_Core_Model_Session
    */
    protected function _getCoreSession(){
            return Mage::getSingleton('core/session');
    }
    
    /**
    * get Affiliate Payment Helper
    *
    * @return Magestore_Affiliateplus_Helper_Payment
    */
    protected function _getPaymentHelper(){
            return Mage::helper('affiliateplus/payment');
    }
    
    /**
    * getConfigHelper
    *
    * @return Magestore_Affiliateplus_Helper_Config
    */
    protected function _getConfigHelper(){
           return Mage::helper('affiliateplus/config');
    }
    
    public function confirmAction()
    {
        if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){
            return;
        }
        $session = Mage::getSingleton('affiliateplus/session');
        $params = $this->getRequest()->getParams();
        $payment = Mage::getModel('affiliateplus/payment')->setData($params);
        if($this->_getAccountHelper()->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        if($payment->getMoneybookerEmail()){
            $payment->setEmail($payment->getMoneybookerEmail());
        }
        Mage::register('confirm_payment_data',$payment);
        //Zend_Debug::dump($payment->getData());die('1');
        $session->setPayment($payment);
        $session->setPaymentMethod($payment->getPaymentMethod());
    	$this->loadLayout();
    	$this->getLayout()->getBlock('head')->setTitle($this->__('Confirm'));
    	$this->renderLayout();
    }
    
    public function requestPaymentAction(){
        if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
    	if ($this->_getAccountHelper()->accountNotLogin())
    		return $this->_redirect('affiliateplus/account/login');
    	$paymentObject = Mage::getSingleton('affiliateplus/session')->getPayment();
        //Zend_Debug::dump(Mage::getSingleton('affiliateplus/session')->getPayment()->getData());die('1');
    	$paymentCodes = $this->_getPaymentHelper()->getAvailablePaymentCode();
    	
    	if (!count($paymentCodes)){
    		$this->_getCoreSession()->addError($this->__('There is no payment method on file for your account. Please update your details or contact us to solve the problem.'));
    		return $this->_redirect('affiliateplus/index/payments');
    	}elseif (count($paymentCodes) == 1){
    		$paymentCode = $paymentObject->getData('payment_method');
    		if (!$paymentCode) $paymentCode = current($paymentCodes);
    	}else
	    	$paymentCode = $paymentObject->getData('payment_method');
    	
		if(!$paymentCode){
			$this->_getCoreSession()->addNotice($this->__('Please chose an available payment method!'));
			return $this->_redirect('affiliateplus/index/paymentForm',$paymentObject->getData());
		}
		
    	if (!in_array($paymentCode,$paymentCodes)){
    		$this->_getCoreSession()->addError($this->__('This payment method is not available, please choose an alternative payment method.'));
			return $this->_redirect('affiliateplus/index/paymentForm',$paymentObject->getData());
    	}
    	$account = $this->_getAccountHelper()->getAccount();
    	$store = Mage::app()->getStore();
    	
    	$amount = $paymentObject->getData('amount');
    	$amount = $amount / $store->convertPrice(1);
    	if ($amount < $this->_getConfigHelper()->getPaymentConfig('payment_release')){
			$this->_getCoreSession()->addNotice($this->__('The minimum balance required to request withdrawal is %s'
				,Mage::helper('core')->currency($this->_getConfigHelper()->getPaymentConfig('payment_release'),true,false)));
    		return $this->_redirect('affiliateplus/index/paymentForm');
    	}
		
		if($amount > $account->getBalance()){
			$this->_getCoreSession()->addError($this->__('The withdrawal requested cannot exceed your current balance (%s).'
    			,Mage::helper('core')->currency($account->getBalance(),true,false)));
				
			return $this->_redirect('affiliateplus/index/paymentForm');
		}
    	
    	$payment = Mage::getModel('affiliateplus/payment')
    		->setPaymentMethod($paymentCode)
    		->setAmount($amount)
    		->setAccountId($account->getId())
    		->setAccountName($account->getName())
    		->setAccountEmail($account->getEmail())
    		->setRequestTime(now())
    		->setStatus(1)
    		->setIsRequest(1)
    		->setIsPayerFee(0);
    	if ($this->_getConfigHelper()->getPaymentConfig('who_pay_fees') == 'payer' && $paymentCode == 'paypal')
    		$payment->setIsPayerFee(1);
    	
    	if ($payment->hasWaitingPayment()){
    		$this->_getCoreSession()->addError($this->__('You are having a pending request!'));
    		return $this->_redirect('affiliateplus/index/payments');
    	}
    	
    	if ($this->_getConfigHelper()->getSharingConfig('balance') == 'store')
    		$payment->setStoreIds($store->getId());
    	
    	$paymentMethod = $payment->getPayment();
    	
    	$paymentObj = new Varien_Object(array(
    		'payment_code'	=> $paymentCode,
    		'required'		=> true,
    	));
    	Mage::dispatchEvent("affiliateplus_request_payment_action_$paymentCode",array(
    		'payment_obj'	=> $paymentObj,
    		'payment'		=> $payment,
    		'payment_method'=> $paymentMethod,
    		'request'		=> $this->getRequest(),
    	));
    	$paymentCode = $paymentObj->getPaymentCode();
    	
    	if ($paymentCode == 'paypal'){
    		$paypalEmail = $paymentObject->getData('paypal_email');
    		
    		//Change paypal email for affiliate account
    		if ($paypalEmail && $paypalEmail != $account->getPaypalEmail()){
    			$accountModel = Mage::getModel('affiliateplus/account')
	    			->setStoreId($store->getId())
	    			->load($account->getId());
	    		try {
	    			$accountModel->setPaypalEmail($paypalEmail)
	    				->setId($account->getId())
	    				->save();
	    		}catch (Exception $e){}
    		}
    		
    		$paypalEmail = $paypalEmail ? $paypalEmail : $account->getPaypalEmail();
    		if ($paypalEmail){
    			$paymentMethod->setEmail($paypalEmail);
    			$paymentObj->setRequired(false);
    		}
    	}
    	
    	if ($paymentObj->getRequired()){
    		$this->_getCoreSession()->addNotice($this->__('Please fill out all required fields in the form below.'));
    		return $this->_redirect('affiliateplus/index/paymentForm',$this->getRequest()->getPost());
    	}
    	
    	// Save request payment for affiliate account
    	try {
            $payment->save();
            $paymentMethod->savePaymentMethodInfo();
            $payment->sendMailRequestPaymentToSales();
            $this->_getCoreSession()->addSuccess($this->__('Your request has been sent to admin for approval.'));
            Mage::getSingleton('affiliateplus/session')->setPayment(null);
            Mage::getSingleton('affiliateplus/session')->setPaymentMethod(null);
    	}catch (Exception $e){
    		$this->_getCoreSession()->addError($e->getMessage());
    	}
    	
    	return $this->_redirect('affiliateplus/index/payments');
    }
    
}