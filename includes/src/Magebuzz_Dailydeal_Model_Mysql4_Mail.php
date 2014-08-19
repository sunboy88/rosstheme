<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Model_Mysql4_Mail extends Mage_Core_Model_Mysql4_Abstract
{
  public function _construct(){
    $this->_init('dailydeal/mail', 'deal_email_id');
  }
}