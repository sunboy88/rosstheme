<?php

class Magebuzz_Dailydeal_Block_Adminhtml_Dailydeal_Report extends Mage_Adminhtml_Block_Widget_Grid
{
  function __construct() {
    $this->_blockGroup = 'dailydeal';
    $this->_controller = 'adminhtml_dailydeal';
  }
}