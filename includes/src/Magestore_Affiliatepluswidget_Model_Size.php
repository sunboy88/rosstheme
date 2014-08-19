<?php

class Magestore_Affiliatepluswidget_Model_Size extends Varien_Object
{
	protected $_sizes;
	
	protected function _construct(){
		$this->_sizes = array(
			'168x145'	=> array(
				'label'		=> Mage::helper('affiliatepluswidget')->__('168x145 Single Product'),
				'width'		=> 168,
				'height'	=> 145,
				'rows'		=> 1,
				'columns'	=> 1,
			),
			'300x250'	=> array(
				'label'		=> Mage::helper('affiliatepluswidget')->__('300x250 Medium Rectangle'),
				'width'		=> 300,
				'height'	=> 250,
				'rows'		=> 3,
				'columns'	=> 2,
			),
			'336x300'	=> array(
				'label'		=> Mage::helper('affiliatepluswidget')->__('336x300 Large Rectangle'),
				'width'		=> 336,
				'height'	=> 300,
				'rows'		=> 3,
				'columns'	=> 2,
			),
			'728x136'	=> array(
				'label'		=> Mage::helper('affiliatepluswidget')->__('728x136 Wide Rectangle'),
				'width'		=> 728,
				'height'	=> 136,
				'rows'		=> 1,
				'columns'	=> 5,
			),
			'728x175'	=> array(
				'label'		=> Mage::helper('affiliatepluswidget')->__('728x175 Wide Rectangle Fully'),
				'width'		=> 728,
				'height'	=> 175,
				'rows'		=> 1,
				'columns'	=> 5,
			),
			'160x450'	=> array(
				'label'		=> Mage::helper('affiliatepluswidget')->__('160x450 Wide Skyscraper'),
				'width'		=> 160,
				'height'	=> 450,
				'rows'		=> 6,
				'columns'	=> 1,
			),
			'160x600'	=> array(
				'label'		=> Mage::helper('affiliatepluswidget')->__('160x600 Wide Skyscraper Fully'),
				'width'		=> 160,
				'height'	=> 600,
				'rows'		=> 6,
				'columns'	=> 1,
			),
		);
		return parent::_construct();
	}
	
	public function getOptionArray(){
		$options = array();
		foreach ($this->_sizes as $key => $value)
			$options[$key] = $value['label'] ? $value['label'] : $key;
		return $options;
	}
	
	public function getOptionHash(){
		$options = array();
		foreach ($this->_sizes as $key => $value)
			$options[] = array(
				'value'	=> $key,
				'label' => $value['label'] ? $value['label'] : $key,
			);
		return $options;
	}
	
	public function getSize($key){
		return $this->_sizes[$key];
	}
}