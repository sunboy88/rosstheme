<?php

class Olegnax_Ajaxcart_Model_Observer
{
	const AJAXCART_ROUTE = 'oxajax';

	public function saveUrl($observer)
	{
		$_currentUrl = Mage::helper('core/url')->getCurrentUrl();
		if (strpos($_currentUrl, self::AJAXCART_ROUTE ) === false) {
			Mage::getSingleton('core/session', array('name' => 'frontend'))->setData('oxajax_referrer',$_currentUrl);
		}
	}

}