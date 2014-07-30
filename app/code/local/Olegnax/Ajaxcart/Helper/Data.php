<?php

class Olegnax_Ajaxcart_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Retrieve config value for store by path
	 *
	 * @param string $path
	 * @param string $section
	 * @param int $store
	 * @return mixed
	 */
	public function getCfg($path, $section = 'oxajax', $store = NULL)
	{
		return Mage::helper('olegnaxall')->getCfg($path, $section, $store);
	}
}