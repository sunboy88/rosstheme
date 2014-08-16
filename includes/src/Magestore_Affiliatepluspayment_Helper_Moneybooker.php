<?php

class Magestore_Affiliatepluspayment_Helper_Moneybooker extends Mage_Core_Helper_Abstract
{
    protected $_errorMsg = array();
    
    public function __construct() {
        $this->_errorMsg = array(
            'CANNOT_LOGIN'  => $this->__('Moneybooker: Email address and/or API/MQI password are incorrect, please check your  configuration'),
            'LOGIN_INVALID' => $this->__('Moneybooker: Email address and/or password were not provided'),
            'PAYMENT_DENIED' => $this->__('Moneybooker: Check in your account profile that the API is enabled and you are posting your requests from the IP address specified'),
            'BALANCE_NOT_ENOUGH' => $this->__('Moneybooker: Sending amount exceeds account balance.'),
            'LOCK_LEVEL_9'  => $this->__('Moneybooker: This account is currently locked.'),
            'INVALID_EMAIL' => $this->__('Moneybooker: This email is invalid.'),
            'SESSION_EXPIRED' => $this->__('Moneybooker: Transfer failed.'),
        );
    }
    
    public function getErrorMessages(){
        return $this->_errorMsg;
    }

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

    public function getSessionId($data, $storeId = null){
        $mailDefault = Mage::getStoreConfig('affiliateplus_payment/moneybooker/user_mechant_email_default',$storeId);
		if($mailDefault)
			$merchant_email = Mage::getStoreConfig('moneybookers/settings/moneybookers_email',$storeId);
        $merchant_email = Mage::getStoreConfig('affiliateplus_payment/moneybooker/moneybooker_email',$storeId);
        $password = Mage::getStoreConfig('affiliateplus_payment/moneybooker/moneybooker_password',$storeId);
        $subject = Mage::getStoreConfig('affiliateplus_payment/moneybooker/notification_subject',$storeId);
        $subject = urlencode($subject);
        $note = Mage::getStoreConfig('affiliateplus_payment/moneybooker/notification_note',$storeId);
        $note = urlencode($note);
        $whoPayFees = Mage::getStoreConfig('affiliateplus/payment/who_pay_fees',$storeId);
        $url = 'https://www.moneybookers.com/app/pay.pl?action=prepare&email='.$merchant_email.'&password='.  md5($password);
        if(isset($data['amount'])){
            $amount = floatval($data['amount']);
            $fee = Mage::getModel('affiliatepluspayment/moneybooker')->getEstimateFee($amount,$whoPayFees);
            if($whoPayFees == 'recipient'){
                $amount = $amount - $fee;
            }
            $url .= '&amount='.$amount;
        }
        if(isset($data['currency']))
            $url .= '&currency='.$data['currency'];
        if(isset($data['email']))
            $url .= '&bnf_email='.$data['email'];
        if(!$subject)
			$subject = 'Affiliate';
		$url .= '&subject='.$subject;
        if(!$note)
			$note = 'Affiliate';
		$url .= '&note='.$note;
        
        
        $data = $this->readXml($url);
        $xml = simplexml_load_string($data);
        if($xml && $xml->error)
            if($xml->error->error_msg)
                return (string)$xml->error->error_msg;
        if($xml->sid)
            return (string)$xml->sid;
        return '';
    }
    
    public function readXml($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);         //set the $url to where your request goes
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //set this flag for results to the variable
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //This is required for HTTPS certs if
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //you don't have some key/password action

        /* execute the request */
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function isError($code){
        if (in_array($code, array_keys($this->_errorMsg))) {
            return true;
        }
//        if($code == Magestore_Affiliatepluspayment_Helper_Moneybooker::CANNOT_LOGIN || $code == Magestore_Affiliatepluspayment_Helper_Moneybooker::LOGIN_INVALID) {
//            return true;
//        }
        return false;
    }

    /**
     * 
     * @param array $data (amount,currency,moneybooker email)
     * @return boolean
     */
    public function transferMoney($data){
        $sid = $this->getSessionId($data);
        if($this->isError($sid)) {
            return $sid;
        }
        $url = 'https://www.moneybookers.com/app/pay.pl?action=transfer&sid=';
        if($sid)
            $url .= $sid;
        $string = $this->readXml($url);
        $xml = simplexml_load_string($string);       
        if($xml) {
            if ($xml->error) {
                if ($xml->error->error_msg)
                    return (string)$xml->error->error_msg;
            } elseif ($xml->transaction) {
                if((string)$xml->transaction->id)
                   return (string)$xml->transaction->id;
            }
        }
        return '';
    }
    
    public function payoutByApi($account, $amount, $storeId = null, $paymentId = null) {
        if (!$storeId) {
            $stores = Mage::app()->getStores();
            foreach ($stores as $store) {
                $storeIds[] = $store->getId();
            }
        } else {
            $storeIds = $storeId;
        }
        $moneybooker_email = $account->getMoneybookerEmail();
        $whoPayFees = Mage::getStoreConfig('affiliateplus/payment/who_pay_fees',$storeId);
        $baseCurrencyCode = Mage::app()->getStore($storeId)->getBaseCurrencyCode();
        $data = array(
            'amount'=>$amount,
            'currency'=>$baseCurrencyCode,
            'email'=>$moneybooker_email
        );
        
        $payment = Mage::getModel('affiliateplus/payment')
                ->load($paymentId)
                ->setId($paymentId)
                ->setPaymentMethod('moneybooker')
                ->setAmount($amount)
                ->setAccountId($account->getId())
                ->setAccountName($account->getName())
                ->setAccountEmail($account->getEmail())
//                ->setRequestTime(now())
                ->setStoreIds(implode(',', $storeIds))
//                ->setStatus(1)
//                ->setIsRequest(0)
                ->setIsPayerFee(0);
        if ($account->getData('is_created_by_recurring')) {
            $payment->setData('is_created_by_recurring', 1)
                ->setData('is_recurring', 1);
        }
        if (!$paymentId) {
            $payment->setRequestTime(now())
                ->setStatus(1)
                ->setIsRequest(0);
        }
        if (Mage::getStoreConfig('affiliateplus/payment/who_pay_fees',$storeId) == 'payer')
            $payment->setIsPayerFee(1);
        
        try{
            $tranId = '';
            try {
                $tranId = $this->transferMoney($data);
            } catch (Exception $e){
                
            }
            if($this->isError($tranId)){
                $payment->setMessageCode($tranId);
                $payment->setErrorMessage($this->_errorMsg[$tranId]);
                $tranId = null;
                $status = 1;
            }else{
                $status = 3;
            }
            $moneybookerModel = $payment->getPayment();
            $fee = $moneybookerModel->getEstimateFee($amount, $whoPayFees);
            try{
                $payment->setData('affiliateplus_account', $account);
                $payment->setPaymentMethod('moneybooker')
                        ->setFee($fee)
                        ->setStatus($status) //complete
//                        ->setData('is_created_by_recurring', 1)
                        ->save();
                $moneybookerModel
                        ->setEmail($account->getMoneybookerEmail())
                        ->setTransactionId($tranId)
                        ->savePaymentMethodInfo();
//                try{
//                    $moneybookerModel->save();
//                }  catch (Exception $e){
//
//                }
                    /*$recipe = $amount;
                    if($whoPayFees == 'recipient')
                        $recipe = $amount - $fee;
                    $account->setBalance($account->getBalance() - $amount)
                            ->setTotalCommissionReceived($account->getTotalCommissionReceived() + $recipe)
                            ->setTotalPaid($account->getTotalPaid() + $amount)
                            ->save();*/
//                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('affiliatepluspayment')->__('The payment has been made successfully.'));
            }  catch (Exception $e){
//                    Mage::getSingleton('core/session')->addError($e);
            }
        }  catch (Exception $e){
//            Mage::getSingleton('core/session')->addError($e);
        }
        return $payment;
    }
}