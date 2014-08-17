<?php
class Trmmarketing_Twiliosmsbridge_Block_Adminhtml_Twiliosmsbridge extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_twiliosmsbridge';
    $this->_blockGroup = 'twiliosmsbridge';
    $this->_headerText = Mage::helper('twiliosmsbridge')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('twiliosmsbridge')->__('Add Item');
    parent::__construct();
  }
}