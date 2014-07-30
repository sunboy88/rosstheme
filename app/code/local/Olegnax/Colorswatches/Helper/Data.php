<?php

class Olegnax_Colorswatches_Helper_Data extends Mage_Core_Helper_Abstract
{

	/**
	 * Retrieve config value for store by path
	 *
	 * @param string $path
	 * @param string $section
	 * @param int $store
	 * @return mixed
	 */
	public function getCfg($path, $section = 'olegnaxcolorswatches', $store = NULL)
	{
		$module = Mage::app()->getRequest()->getModuleName();
		if ( $path == 'main/replace_image' && $module == 'oxajax' ) {
			return 0;
		} else
			return Mage::helper('olegnaxall')->getCfg($path, $section, $store);
	}

	public function switchTemplate()
	{
		$template = 'olegnax/colorswatches/media.phtml';
		if ( Mage::helper('athlete')->getCfg('images/zoom') == 'lightbox') {
			$template = 'olegnax/colorswatches/lightbox.phtml';
		}
		if ( Mage::helper('athlete')->getCfg('images/zoom') == 'cloudzoom') {
			$template = 'olegnax/colorswatches/cloudzoom.phtml';
		}
		return $template;
	}
}