<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Dailydeal_Block_Adminhtml_Dailydeal_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('dailydeal_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('dailydeal')->__('Deal Information'));
	}

	protected function _beforeToHtml(){
		
		$this->addTab('product_section', array(
			'label'	 => Mage::helper('dailydeal')->__('Products'),
			'title'	 => Mage::helper('dailydeal')->__('Products'),			
			'url'		  => $this->getUrl('*/*/product',array('_current'=>true,'id'=>$this->getRequest()->getParam('id'))),
		  	'class'     => 'ajax',
		));
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('dailydeal')->__('Infomation'),
			'title'	 => Mage::helper('dailydeal')->__('Infomation'),
			'content'	 => $this->getLayout()->createBlock('dailydeal/adminhtml_dailydeal_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}