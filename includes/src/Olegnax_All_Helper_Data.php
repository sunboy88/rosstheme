<?php

class Olegnax_All_Helper_Data extends Mage_Core_Helper_Abstract
{
/**
	 * Retrieve config value for store by path
	 *
	 * @param string $path
	 * @param string $section
	 * @param int $store
	 * @return mixed
	 */
	public function getCfg($path, $section = 'olegnaxall', $store = NULL)
	{
		if ($store == NULL) {
			$store = Mage::app()->getStore()->getId();
		}
		if (empty($path)) {
			$path = $section;
		} else {
			$path = $section . '/' . $path;
		}
		return Mage::getStoreConfig($path, $store);
	}
}