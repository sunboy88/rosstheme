<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Model_Status extends Varien_Object
{
  // const STATUS_ENABLED = 1;
  // const STATUS_DISABLED  = 2;

  // static public function getOptionArray(){
  //  return array(
  //    self::STATUS_ENABLED  => Mage::helper('dailydeal')->__('Enabled'),
  //    self::STATUS_DISABLED   => Mage::helper('dailydeal')->__('Disabled')
  //  );
  // }
  
  // static public function getOptionHash(){
  //  $options = array();
  //  foreach (self::getOptionArray() as $value => $label)
  //    $options[] = array(
  //      'value' => $value,
  //      'label' => $label
  //    );
  //  return $options;
  // }
  
  public function getStatusList(){
    $statusList=array(
      1 =>  Mage::helper('dailydeal')->__('Queued'),
      2 =>  Mage::helper('dailydeal')->__('Running'),
      3 =>  Mage::helper('dailydeal')->__('Ended'),
      4 =>  Mage::helper('dailydeal')->__('Disabled')
    );
    return $statusList;
  }

  public function getSettingStatusList() {
    return array(
      1 =>  Mage::helper('dailydeal')->__('Enabled'),
      4 =>  Mage::helper('dailydeal')->__('Disabled')
    );
  }
}