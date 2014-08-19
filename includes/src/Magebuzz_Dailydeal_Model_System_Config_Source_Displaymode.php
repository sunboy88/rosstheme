<?php 
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Model_System_Config_Source_Displaymode
{
  public function toOptionArray()
  {
  return array(
    array('value' => 'tab', 'label'=>Mage::helper('adminhtml')->__('Deals Tab')),
      array('value' => 'grid', 'label'=>Mage::helper('adminhtml')->__('Grid Deals'))
  );
  }

}
