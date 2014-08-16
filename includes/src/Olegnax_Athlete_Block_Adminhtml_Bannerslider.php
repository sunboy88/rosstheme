<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Block_Adminhtml_Bannerslider extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_bannerslider';
		$this->_blockGroup = 'athlete';
		$this->_headerText = Mage::helper('athlete')->__('Athlete Banner Slides Manager');
		$this->_addButtonLabel = Mage::helper('athlete')->__('Add Slide');
		parent::__construct();
	}
}