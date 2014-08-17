<?php
class Magestore_Onestepcheckout_AjaxController extends Mage_Core_Controller_Front_Action {
	
    public function add_giftwrapAction()
    {
        $remove = $this->getRequest()->getPost('remove', false);
		$session = Mage::getSingleton('checkout/session');
        if(!$remove){
            $session->setData('onestepcheckout_giftwrap', 1);
        }else{
            $session->unsetData('onestepcheckout_giftwrap');
            $session->unsetData('onestepcheckout_giftwrap_amount');
        }        
        $this->loadLayout(false);
        $this->renderLayout();
    }
	
    public function forgotPasswordAction()
    {
        $email = $this->getRequest()->getPost('email', false);

        if (!Zend_Validate::is($email, 'EmailAddress')) {
            $result = array('success'=>false);
        }
        else{
            $customer = Mage::getModel('customer/customer')
                            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                            ->loadByEmail($email);
            if ($customer->getId()) {
                try {
                    $newPassword = $customer->generatePassword();
                    $customer->changePassword($newPassword, false);
                    $customer->sendPasswordReminderEmail();
                    $result = array('success'=>true);
                }catch (Exception $e){
                    $result = array('success'=>false, 'error'=>$e->getMessage());
                }
            }else{
                $result = array('success'=>false, 'error'=>'notfound');
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    public function loginAction()
    {        
        $username = $this->getRequest()->getPost('onestepcheckout_username', false);
        $password = $this->getRequest()->getPost('onestepcheckout_password',  false);
        $session = Mage::getSingleton('customer/session');
        $result = array('success' => false);
        if ($username && $password) {
            try {
                $session->login($username, $password);
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            if (! isset($result['error'])) {
                $result['success'] = true;
            }
        } else {
            $result['error'] = $this->__('Please enter a username and password.');
        }        
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
}