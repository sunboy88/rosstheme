<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Helper_Data extends Mage_Core_Helper_Abstract {
  const XML_PATH_SELECTED_EMAIL_TEMPLATE					= 'dailydeal/deal_subscriber/email_template';
  const XML_PATH_SELECTED_EMAIL_SENDER_IDENTITY   = 'dailydeal/deal_subscriber/email_sender';
  const XML_PATH_INTRO_MESSAGE   									= 'dailydeal/deal_subscriber/intro_message';
  const XML_PATH_THANKS_MESSAGE   								= 'dailydeal/deal_subscriber/thanks_message';	

  public function isActiveDailydeal(){
    if(Mage::getStoreConfig('dailydeal/general/enable')){
      return true;
    }
    return false;
  }

  public function getIntroMessage(){
    return Mage::getStoreConfig(self::XML_PATH_INTRO_MESSAGE);
  }

  public function getThanksMessage(){
    return Mage::getStoreConfig(self::XML_PATH_THANKS_MESSAGE);
  }

  public function getIsoDate($str) {
    return Mage::app()->getLocale()->date($str)->getIso();
  }

  public function unsubscribe($emailId){
    $model = Mage::getModel('dailydeal/mail')->load($emailId);
    $model->setStatus(2);
    $model->save();
  }	

  public function sendNotificationEmails() {
    $deals  = Mage::getModel('dailydeal/deal')->getCollection()
    ->addFieldToFilter('status', 2)
    ->addFieldToFilter('quantity', array("gt"=>'0'));
    $emails = Mage::getModel('dailydeal/mail')->getCollection()
    ->addFieldToFilter('status', 1);
    $storeId = Mage::app()->getStore()->getId();
    $templateId = Mage::getStoreConfig(self::XML_PATH_SELECTED_EMAIL_TEMPLATE, $storeId);			
    $mailer = Mage::getModel('core/email_template_mailer');

    if (count($emails)) {
      foreach ($emails as $email) { 
        $emailInfo = Mage::getModel('core/email_info');
        $emailId = $email->getId();
        $to = $email->getEmail();
        $name = $email->getCustomerName();
        $confirmCode = $email->getSubscriberConfirmCode();
        $linkConfirm = Mage::getUrl('dailydeal/index/unsubscribe',array('id'=> $emailId, 'code' => $confirmCode));
        $emailInfo->addTo($to, $name);
        $mailer->addEmailInfo($emailInfo);
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_SELECTED_EMAIL_SENDER_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
        'deals'        => $deals,
        'customer_name' => $name,
        'unsubscribe_link' => $link
        )
        );
        $mailer->send();
      }
    }				
  }
	public function checkEmailSubscriber($email) {
		$emails = Mage::getModel('dailydeal/mail')->getCollection()
							->addFieldToFilter('email', $email)
							->addFieldToFilter('status', 1);
		if(count($emails) > 0){
			return true;
		}else{
			return false;
		}					
	}
	
	public function getDisplayMode() {
		return Mage::getStoreConfig('dailydeal/general/display_mode');
	}

  public function updateAllDealStatus() {
    $collection = Mage::getModel('dailydeal/deal')->getCollection();
    foreach ($collection as $deal) {
      $deal->updateStatus();
    }
  }

  public function enableSocialSharing() {
    return Mage::getStoreConfig('dailydeal/general/enable_sharing');
  }

  public function getSharingMessageTemplate() {
    return Mage::getStoreConfig('dailydeal/general/sharing_template_message');
  }

  public function getDealByProductId($product_id) {
    $deal=Mage::getModel('dailydeal/deal');
    $dealcollection=Mage::getModel('dailydeal/deal')
    ->getCollection()
    ->addFieldToFilter('product_id',$product_id)
    ->setOrder('deal_price', 'ASC');
    $tableStore = Mage::getSingleton('core/resource')->getTableName('dailydeal_deal_store');
    $dealcollection ->getSelect()->join(array('t2' => $tableStore),'main_table.deal_id = t2.deal_id','t2.store_id');
    $dealcollection->addFieldToFilter('store_id', array('in' => array(0,Mage :: app()->getStore()->getId())));
    if($dealcollection->getSize()){
      return $deal->load($dealcollection->getFirstItem()->getId());
    }
    return $deal;
  }

  public function getTodayDeals() {
    // Must use gmtTimestamp to return GMT + 0 time
    $now_time = date('Y-m-d H:i:s', Mage::getModel('core/date')->gmtTimestamp());
    $dealcollection = Mage::getModel('dailydeal/deal')->getCollection();
    $dealcollection
      ->addFieldToFilter('start_time',array('to'=>$now_time))
      ->addFieldToFilter('end_time',array('from'=>$now_time))
      ->addFieldToFilter('status',array('neq'=>4))
      ->setOrder('deal_price', 'ASC');
    $tableStore = Mage::getSingleton('core/resource')->getTableName('dailydeal_deal_store');
    $dealcollection ->getSelect()->join(array('dds' => $tableStore),'main_table.deal_id = dds.deal_id','dds.store_id');
    $dealcollection->addFieldToFilter('dds.store_id', array('in' => array(0,Mage :: app()->getStore()->getId())));
    $dealcollection->filterProductByCurrentStore();
    return $dealcollection;
  }

  public function getPreviousDeals() {
    // Must use gmtTimestamp to return GMT + 0 time
    $now_time = date('Y-m-d H:i:s', Mage::getModel('core/date')->gmtTimestamp());
    $dealcollection = Mage::getModel('dailydeal/deal')->getCollection();
    $dealcollection
      ->addFieldToFilter('end_time',array('to'=>$now_time))
      ->addFieldToFilter('status',array('neq' => '4'))
      ->setOrder('deal_price', 'ASC');
    $tableStore = Mage::getSingleton('core/resource')->getTableName('dailydeal_deal_store');
    $dealcollection ->getSelect()->join(array('dds' => $tableStore),'main_table.deal_id = dds.deal_id','dds.store_id');
    $dealcollection->addFieldToFilter('dds.store_id', array('in' => array(0,Mage :: app()->getStore()->getId())));
    $dealcollection->filterProductByCurrentStore();
    return $dealcollection;
  }

  public function getComingDeals() {
    // Must use gmtTimestamp to return GMT + 0 time
    $now_time = date('Y-m-d H:i:s', Mage::getModel('core/date')->gmtTimestamp());
    $dealcollection = Mage::getModel('dailydeal/deal')->getCollection();
    $dealcollection
      ->addFieldToFilter('start_time',array('from'=>$now_time))
      ->addFieldToFilter('status',array('neq' => '4'))
      ->setOrder('deal_price', 'ASC');
    $tableStore = Mage::getSingleton('core/resource')->getTableName('dailydeal_deal_store');
    $dealcollection ->getSelect()->join(array('dds' => $tableStore),'main_table.deal_id = dds.deal_id','dds.store_id');
    $dealcollection->addFieldToFilter('dds.store_id', array('in' => array(0,Mage :: app()->getStore()->getId())));
    $dealcollection->filterProductByCurrentStore();
    return $dealcollection;
  }
}