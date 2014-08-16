<?php
class Magestore_Affiliatepluspayment_Adminhtml_PaymentController extends Mage_Core_Controller_Front_Action
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
    
    public function getStoreId(){
        $store = $this->getRequest()->getParam('store');
        return Mage::app()->getStore($store)->getId();
    }
   
    
    public function verifyMoneybookerAction(){
        $storeId = $this->getStoreId();
        $request = $this->getRequest();
        $default = $request->getParam('default');
        if($default)
			$email = Mage::getStoreConfig('moneybookers/settings/moneybookers_email',$storeId);
        else
            $email = $request->getParam('email');
        $password = $request->getParam('password');
        $subject = $request->getParam('subject');
        $subject = $subject ? $subject: 'Affiliateplus';
        $note = $request->getParam('note');
        $note = $note ? $note: 'Affiliateplus';
        $link = 'https://www.moneybookers.com/app/pay.pl?action=prepare&email='.$email.'&password='.  md5($password).'&amount=1&currency=USD&bnf_email='.$email.'&subject='.$subject.'&note='.$note;
        $data = Mage::helper('affiliatepluspayment/moneybooker')->readXml($link);
        $xml = simplexml_load_string($data);
        $body = '';
        if($xml && $xml->error)
            if($xml->error->error_msg)
                $body = (string)$xml->error->error_msg;
        if($xml && $xml->sid)
            $body = (string)$xml->sid;
        $errors = Mage::helper('affiliatepluspayment/moneybooker')->getErrorMessages();
        if (array_key_exists($body, $errors)) {
            $body = $errors[$body];
        } elseif ($body) {
            $body = 1;
        } else {
            $body = Mage::helper('affiliatepluspayment/moneybooker')->__('Can not connect to Moneybooker server at this time');
        }
        $this->getResponse()->setBody($body);
    }
}