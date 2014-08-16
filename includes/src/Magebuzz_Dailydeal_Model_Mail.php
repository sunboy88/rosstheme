<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Model_Mail extends Mage_Core_Model_Abstract
{
  public function _construct(){
    parent::_construct();
    $this->_init('dailydeal/mail');
  }
  
  public function randomSequence($length=32)
  {
    $id = '';
    $par = array();
    $char = array_merge(range('a','z'),range(0,9));
    $charLen = count($char)-1;
    for ($i=0;$i<$length;$i++){
        $disc = mt_rand(0, $charLen);
        $par[$i] = $char[$disc];
        $id = $id.$char[$disc];
    }
    return $id;
  }
  
  public function saveMail($email, $customerName){    
    if($email){
      $collection = $this->getCollection()->addFieldToFilter('email', $email);      
      if (count($collection) == 0) {
        $this->setEmail($email)
          ->setCustomerName($customerName)
          ->setStatus(1)
          ->setSubscriberConfirmCode($this->randomSequence())
          ->save();     
        return true;
      }
      else {
        $collection->getFirstItem()->setStatus(1)->save();
        return true;
      }
    }
    
    return false;
  }
  
  public function loadByEmail($subscriberEmail)
  {
    $this->addData($this->getResource()->loadByEmail($subscriberEmail));
    return $this;
  }
  
  public function unsubscribe($emailId){
    $this->load($emailId)->setStatus(2)->save();
    return;
  }
}