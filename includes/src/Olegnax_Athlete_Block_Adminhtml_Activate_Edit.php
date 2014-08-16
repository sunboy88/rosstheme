<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Block_Adminhtml_Activate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();

		$this->_blockGroup = 'athlete';
		$this->_controller = 'adminhtml_activate';
		$this->_updateButton('save', 'label', Mage::helper('athlete')->__('Activate Athlete Theme'));
		$this->_removeButton('delete');
		$this->_removeButton('back');
	}

	public function getHeaderText()
	{
		return Mage::helper('athlete')->__('Activate Athlete Theme');
	}
}
