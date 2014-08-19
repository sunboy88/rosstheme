<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Mail extends Mage_Adminhtml_Block_Widget_Grid_Container {
  public function __construct() {
    $this->_controller = 'adminhtml_mail';
    $this->_blockGroup = 'dailydeal';
    $this->_headerText = Mage::helper('dailydeal')->__('Deal Email Manager');
    $this->_addButtonLabel = Mage::helper('dailydeal')->__('Add Deal Email');
    $this->_addButton('import_new_subscribers', array(
      'label'     => Mage::helper('dailydeal')->__('Import Subscribers'),
      'onclick'   => 'setLocation(\'' . $this->_getImportUrl() .'\')',
      'class'     => 'add',
    ));
    parent::__construct();
    $this->removeButton('add');
  }
  
  protected function _getImportUrl() {
    return $this->getUrl('*/*/import', array('_secure' => true));
  }
}