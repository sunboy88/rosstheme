<?php

class Olegnax_Ajaxcart_Helper_Product_Compare extends Mage_Catalog_Helper_Product_Compare
{

	/**
	 * Retrieve remove item from compare list url
	 *
	 * @param   $item
	 * @return  string
	 */
	public function getRemoveUrl($item)
	{
		$params = array(
			'product'=>$item->getId(),
			Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl(
					Mage::getSingleton('core/session', array('name' => 'frontend'))->getData('oxajax_referrer')
				)
		);
		return $this->_getUrl('catalog/product_compare/remove', $params);
	}

	/**
	 * Retrieve clear compare list url
	 *
	 * @return string
	 */
	public function getClearListUrl()
	{
		$params = array(
			Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl(
					Mage::getSingleton('core/session', array('name' => 'frontend'))->getData('oxajax_referrer')
				)
		);
		return $this->_getUrl('catalog/product_compare/clear', $params);
	}

}
