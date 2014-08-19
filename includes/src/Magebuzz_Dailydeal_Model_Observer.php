<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Model_Observer{
  public function catalog_product_get_final_price($observer){
    if (Mage::helper('dailydeal')->isActiveDailydeal()) {
      $product=$observer->getEvent()->getProduct();
      $dealcollection=Mage::getModel('dailydeal/deal')
      ->getCollection()
      ->addFieldToFilter('product_id',$product->getId())
      ->setOrder('deal_price','ASC');
			
      if ($dealcollection->getSize()) {
        $deal=$dealcollection->getFirstItem();
        $price=$deal->getDealPrice();
        $now_time = Mage::getModel('core/date')->gmtTimestamp();
        $en_time = strtotime(Mage::helper('dailydeal')->getIsoDate($deal->getEndTime()));
        if(($en_time > $now_time) && ($deal->getQuantity()) && $deal->getStatus()==2) {
          $product->setFinalPrice($price);
        }
      }
    }
  }

  public function sales_order_place_after($observer){
    if(Mage::helper('dailydeal')->isActiveDailydeal()){
      $order = $observer->getEvent()->getOrder();
      $itemcollection=$order->getItemsCollection();		 	
      foreach($itemcollection as $item){
        $deal=Mage::getModel('dailydeal/deal')
        ->loadByProductId($item->getProductId());		 		
        if($deal->getId()){
          $quantity=abs((int)$deal->getQuantity() - (int)$item->getQtyOrdered());
          $deal->setQuantity($quantity)
          ->save();
        }
      }
    }
  }

  //send email cron job
  public function sendEmailSubscriber(){
    Mage::helper('dailydeal')->sendNotificationEmails();			
  }

  public function setFinalPriceCatalog($observer) {
		$collection = $observer->getCollection();

		if (count($collection)) {
			foreach ($collection as $product) {			
				$deal = Mage::helper('dailydeal')->getDealByProductId($product->getId());
				if ($deal->getId()) {					
					$price = $deal->getDealPrice();
					$now_time = Mage::getModel('core/date')->gmtTimestamp();
					$en_time = strtotime($deal->getEndTime());
					if (($en_time > $now_time) && $deal->getQuantity() && $deal->getStatus()==2) {
						$product->setFinalPrice($price);
					}	
					
				}
			}
		}
	}
}