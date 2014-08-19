<?php


class Ds_Resposiveslider_Block_Adminhtml_Resposiveslider extends Mage_Adminhtml_Block_Widget_Grid_Container{

	public function __construct()
	{

	$this->_controller = "adminhtml_resposiveslider";
	$this->_blockGroup = "resposiveslider";
	$this->_headerText = Mage::helper("resposiveslider")->__("DS Responsive Slider Manager");
	$this->_addButtonLabel = Mage::helper("resposiveslider")->__("Add New Item");
	parent::__construct();
	
	}

}