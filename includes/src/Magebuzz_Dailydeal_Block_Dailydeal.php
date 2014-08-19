<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Dailydeal extends Mage_Core_Block_Template {

  public function getTodayDeals(){
    // Must use gmtTimestamp to return GMT + 0 time
    return Mage::helper('dailydeal')->getTodayDeals();
  }
	
	public function getPreviousDeals() {
    // Must use gmtTimestamp to return GMT + 0 time
    return Mage::helper('dailydeal')->getPreviousDeals();
  }
	
	public function getComingDeals() {
    // Must use gmtTimestamp to return GMT + 0 time
    return Mage::helper('dailydeal')->getComingDeals();
  }
}