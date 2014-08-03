<?php
/**
* @copyright Amasty.
*/ 
class Amasty_Shopby_Block_Adminhtml_Filter extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_filter';
        $this->_headerText = Mage::helper('amshopby')->__('Manage Filters');
        $this->_blockGroup = 'amshopby';
        $this->_addButtonLabel = Mage::helper('amshopby')->__('Load'); 
        parent::__construct();
    }
}