<?php
require_once 'Ebizmarts/SagePaySuite/controllers/PaymentController.php';    
class Magestore_Onestepcheckout_PaymentController extends Ebizmarts_SagePaySuite_PaymentController {
	
	private function _emailIsRegistered($email_address) {
		$model = Mage::getModel('customer/customer');
		$model->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email_address);
		if ($model->getId()) {
			return true;
		}
		else {
			return false;
		}
	}
	
	private function _MAGESTORESaveBilling()
	{
        $post = $this->getRequest()->getPost();
		if (!$post) return;
		$result = new stdClass();
		$error = false;
		$helper = Mage::helper('onestepcheckout');
		
		$billing_data = $this->getRequest()->getPost('billing', array());
		$shipping_data = $this->getRequest()->getPost('shipping', array());
		
		//set checkout method 
		$checkoutMethod = '';
		if (!Mage::helper('customer')->isLoggedIn()) {
			$checkoutMethod = 'guest';
			if ($helper->enableRegistration() || !$helper->allowGuestCheckout()) {
				$is_create_account = $this->getRequest()->getPost('create_account_checkbox');
				$email_address = $billing_data['email'];
				if ($is_create_account || !$helper->allowGuestCheckout()) {
					if ($this->_emailIsRegistered($email_address)) {						
						$error = true;
						Mage::getSingleton('checkout/session')->addError(Mage::helper('onestepcheckout')->__('Email is already registered.'));
					}
					else {
						if (!$billing_data['customer_password'] || $billing_data['customer_password'] == '') {
							$error = true;							
						}
						else if (!$billing_data['confirm_password'] || $billing_data['confirm_password'] == '') {
							$error = true;
						}
						else if ($billing_data['confirm_password'] !== $billing_data['customer_password']) {
							$error = true;
						}
						if ($error) {
							Mage::getSingleton('checkout/session')->addError(Mage::helper('onestepcheckout')->__('Please correct your password.'));
							$this->_redirect('*/*/index');
						}
						else {
							$checkoutMethod = 'register';					
						}
					}
				}
			}
		}
		if ($checkoutMethod != '') $this->getOnepage()->saveCheckoutMethod($checkoutMethod);
		if($checkoutMethod == 'register'){
			$password = $billing_data['customer_password'];
			$this->getOnepage()->getQuote()->getCustomer()->setData('password', $password);
			$this->getOnepage()->getQuote()->setData('customer_email', $billing_data['email']);
			$this->getOnepage()->getQuote()->setData('customer_firstname', $billing_data['firstname']);
			$this->getOnepage()->getQuote()->setData('customer_lastname', $billing_data['lastname']);
			$this->getOnepage()->getQuote()->setData('password_hash', Mage::getModel('customer/customer')->encryptPassword($password));
		}
		//to ignore validation for disabled fields
		$this->setIgnoreValidation();
		
		//resave billing address to make sure there is no error if customer change something in billing section before finishing order
		$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
		$result = $this->getOnepage()->saveBilling($billing_data, $customerAddressId);
		if(isset($result['error']))	{
			$error = true;
			if (is_array($result['message']) && isset($result['message'][0]))
				Mage::getSingleton('checkout/session')->addError($result['message'][0]);
			else 
				Mage::getSingleton('checkout/session')->addError($result['message']);	
		}
		
		//re-save shipping address
		$shipping_address_id = $this->getRequest()->getPost('shipping_address_id', false);
		if($helper->isShowShippingAddress()) {
			if(!isset($billing_data['use_for_shipping']) || $billing_data['use_for_shipping'] != '1')	{				
				$result = $this->getOnepage()->saveShipping($shipping_data, $shipping_address_id);
				if(isset($result['error'])) {
					$error = true;
					if (is_array($result['message']) && isset($result['message'][0]))
						Mage::getSingleton('checkout/session')->addError($result['message'][0]);
					else 
						Mage::getSingleton('checkout/session')->addError($result['message']);
				}
			}
			else {
				$result['allow_sections'] = array('shipping');
                $result['duplicateBillingInfo'] = 'true';
			}
		}
		
		//re-save shipping method
		$shipping_method = $this->getRequest()->getPost('shipping_method', '');
		if(!$this->getOnepage()->getQuote()->isVirtual()) {			
			$result = $this->getOnepage()->saveShippingMethod($shipping_method);
			if(isset($result['error'])) {
				$error = true;
				if (is_array($result['message']) && isset($result['message'][0]))	{					
					Mage::getSingleton('checkout/session')->addError($result['message'][0]);
				}
				else {					
					Mage::getSingleton('checkout/session')->addError($result['message']);
				}
			}
			else {
				Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepage()->getQuote()));				
			}		
		}
		
		//save payment method		
		try {
			$result = array();
			$payment = $this->getRequest()->getPost('payment', array());	
			$result = $helper->savePaymentMethod($payment);
			if($payment){
				$this->getOnepage()->getQuote()->getPayment()->importData($payment);
			}
		}
		catch (Mage_Payment_Exception $e) {
			if ($e->getFields()) {
				$result['fields'] = $e->getFields();
			}
			$result['error'] = $e->getMessage();			
		} catch (Mage_Core_Exception $e) {
			$result['error'] = $e->getMessage();
		} catch (Exception $e) {
			Mage::logException($e);
			$result['error'] = $this->__('Unable to set Payment Method.');
		}
		
		if (isset($result['error'])) {
			$error = true;
			Mage::getSingleton('checkout/session')->addError($result['error']);
		}
	
		//only continue to process order if there is no error
		if (!$error) {
			//newsletter subscribe
			if ($helper->isShowNewsletter()) {
				$news_billing = $this->getRequest()->getPost('billing');		
				// $is_subscriber = $this->getRequest()->getPost('newsletter_subscriber_checkbox', false);	
				$is_subscriber = $news_billing['newsletter_subscriber_checkbox'];					
				if ($is_subscriber) {
					$subscribe_email = '';
					//pull subscriber email from billing data
					if (isset($billing_data['email']) && $billing_data['email'] != '') {
						$subscribe_email = $billing_data['email'];
					}				
					else if (Mage::helper('customer')->isLoggedIn()) {
						$subscribe_email = Mage::helper('customer')->getCustomer()->getEmail();
					}
					//check if email is already subscribed
					$subscriberModel = Mage::getModel('newsletter/subscriber')->loadByEmail($subscribe_email);
					if ($subscriberModel->getId() === NULL) { 
						Mage::getModel('newsletter/subscriber')->subscribe($subscribe_email);					
					}else if($subscriberModel->getData('subscriber_status') !=1 ){
						$subscriberModel->setData('subscriber_status', 1);
						try{
							$subscriberModel->save();
						}catch(Exception $e){
						}
					}
				}
			}	
			//Save OSC Comment
			$session = Mage::getSingleton('checkout/session');
			if ($helper->enableOrderComment()) {
				$comment = $billing_data['onestepcheckout_comment'];
				
				$comment = trim($comment);			
				if ($comment != '') {
					$session->setData('customer_comment', $comment);					
				}
			}
  
			//}
			//Save OSC Survey
			if($helper->enableSurvey()){
				$surveyQuestion = $helper->getSurveyQuestion();
				$surveyValues = unserialize($helper->getSurveyValues());			
				$surveyValue = $billing_data['onestepcheckout-survey'];			
				$surveyFreeText = $billing_data['onestepcheckout-survey-freetext'];
				$surveyAnswer='';				
				if(!empty($surveyValue)){
					if($surveyValue != 'freetext'){
						$surveyAnswer = $surveyValues[$surveyValue]['value'];
					}
					else{
						$surveyAnswer = $surveyFreeText;
					}
				}			
				if($surveyQuestion)
					$session->setData('survey_question', $surveyQuestion);
				if($surveyAnswer)
					$session->setData('survey_answer', $surveyAnswer);		
			}
		}else {
		}

	}
	
	public function onepageSaveOrderAction()
    {
        if ($this->_expireAjax()) {
            return;
        }

		$paymentMethod = $this->getOnepage()->getQuote()->getPayment()->getMethod();               
                
		if((FALSE === strstr(parse_url($this->_getRefererUrl(), PHP_URL_PATH), 'onestepcheckout')) && is_null($this->getRequest()->getPost('billing'))){ // Not OSC, OSC validates T&C with JS and has it own T&C
		   # Validate checkout Terms and Conditions
	        $result = array();
	        if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
	            $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
	            if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
	                $result['success'] = false;
	                $result['response_status'] = 'ERROR';
	                $result['response_status_detail'] = $this->__('Please agree to all the terms and conditions before placing the order.');
	                $this->getResponse()->setBody(Zend_Json::encode($result));
	                return;
	            }
	        }
	        # Validate checkout Terms and Conditions
		}else{

		/** * OSC	*/
			if(FALSE !== Mage::getConfig()->getNode('modules/Idev_OneStepCheckout')){
				$this->_OSCSaveBilling();
			}elseif(FALSE !== Mage::getConfig()->getNode('modules/Magestore_Onestepcheckout')){
				$this->_MAGESTORESaveBilling();
			}
		/*** OSC */
		}
		$paymentData = $this->getRequest()->getPost('payment', array());
		if ($paymentData) {
			//Sanitize payment data
			array_walk($paymentData, array($this, "sanitize_string"));                                        
			$this->getOnepage()->getQuote()->getPayment()->importData($paymentData);                        
		}

		if($dataM = $this->getRequest()->getPost('shipping_method', '')){
			$this->getOnepage()->saveShippingMethod($this->sanitize_string($dataM));
		}

        if($paymentMethod == 'sagepayserver'){
            $this->_forward('saveOrder', 'serverPayment', 'sgps', $this->getRequest()->getParams());
            return;
        }else if($paymentMethod == 'sagepaydirectpro'){
            $this->_forward('saveOrder', 'directPayment', 'sgps', $this->getRequest()->getParams());
            return;
        }else if($paymentMethod == 'sagepayform'){
            $this->_forward('saveOrder', 'formPayment', 'sgps', $this->getRequest()->getParams());
            return;
        }else{
            $this->_forward('saveOrder', 'onepage', 'checkout', $this->getRequest()->getParams());
            return;
        }

    }
	
	public function setIgnoreValidation() {
		$this->getOnepage()->getQuote()->getBillingAddress()->setShouldIgnoreValidation(true);
		$this->getOnepage()->getQuote()->getShippingAddress()->setShouldIgnoreValidation(true);
	}
}
