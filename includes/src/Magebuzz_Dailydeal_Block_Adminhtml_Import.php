<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Import extends Mage_Adminhtml_Block_Widget_Form_Container {
  public function __construct() {
    parent::__construct();
    $this->_updateButton('save', 'label', Mage::helper('dailydeal')->__('Import'));
    $this->_removeButton('delete');
    $this->_blockGroup = 'dailydeal';
    $this->_controller = 'adminhtml';
    $this->_mode = 'import';
  }

  public function getHeaderText(){
    return Mage::helper('dailydeal')->__('Import subscribers');
  }
}