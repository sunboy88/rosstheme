<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Block_Adminhtml_Athleterevolution extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_athleterevolution';
		$this->_blockGroup = 'athleteslideshow';
		$this->_headerText = Mage::helper('athleteslideshow')->__('Revolution Slides Manager');
		$this->_addButtonLabel = Mage::helper('athleteslideshow')->__('Add Slide');
		parent::__construct();
	}
}