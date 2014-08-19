<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_IndexController extends Mage_Core_Controller_Front_Action {
  public function indexAction(){
    if(Mage::helper('dailydeal')->isActiveDailydeal()){
      $this->loadLayout();
      $this->getLayout()->getBlock('head')->setTitle(Mage::helper('dailydeal')->__('Daily Deal'));
      $this->renderLayout();
    }
    else {
      $this->_redirect('');
    }
  }
  
  public function unsubscribeAction() {
    $id    = (int) $this->getRequest()->getParam('id');
    $code  = (string) $this->getRequest()->getParam('code');        
    if ($id && $code) {
      $session = Mage::getSingleton('core/session');
      try {
        Mage::getModel('dailydeal/mail')->load($id)->unsubscribe();
        $session->addSuccess($this->__('You have unsubscribed successfully. Thank you!'));
      }
      catch (Mage_Core_Exception $e) {
        $session->addException($e, $e->getMessage());
      }
      catch(Exception $e) {
        $session->addException($e, $this->__('There was a problem with your unsubscription. Please try again.'));
      }
    }       
    Mage::app()->getResponse()->setRedirect(Mage::getUrl());
  }
  
  public function templateAction(){
    $this->loadLayout();
    $this->renderLayout();
  }
  
  public function subscribeAction() {
    $email              = $this->getRequest()->getParam('email_address');
    $customerName = $this->getRequest()->getParam('customer_name');
    $model = Mage::getModel('dailydeal/mail');
    $flag = $model->saveMail($email, $customerName);        
    $result = array();              
    if ($flag) {
      $result['error'] = false;
      $result['message'] = Mage::helper('dailydeal')->getThanksMessage();
    } else {
      $result['error'] = true;
      $result['message'] = Mage::helper('dailydeal')->__('There was already a subscriber with this email. Please use another email.');
    }       
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }   
  
}