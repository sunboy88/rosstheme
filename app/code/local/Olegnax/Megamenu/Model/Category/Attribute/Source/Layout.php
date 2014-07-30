<?php

class Olegnax_Megamenu_Model_Category_Attribute_Source_Layout extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions()
	{
		if (!$this->_options) {
			$this->_options = array(
				array(
					'value' => 'menu',
					'label' => Mage::helper('catalog')->__('Top / Menu + Right / Bottom'),
				),
				array(
					'value' => 'top_menu',
					'label' => Mage::helper('catalog')->__('Top + Menu / Right / Bottom'),
				),
				array(
					'value' => 'top_menu_bottom',
					'label' => Mage::helper('catalog')->__('Top + Menu + Bottom / Right'),
				),
			);
		}
		return $this->_options;
	}
}
