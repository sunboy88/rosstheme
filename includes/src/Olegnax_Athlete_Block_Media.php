<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athlete_Block_Media extends Mage_Catalog_Block_Product_View_Media
{

	protected function _beforeToHtml()
	{
		if ( $this->helper('athlete')->getCfg('images/zoom') == 'lightbox') {
			$this->setTemplate('olegnax/product/view/lightbox.phtml');
		}
		if ( $this->helper('athlete')->getCfg('images/zoom') == 'cloudzoom') {
			$this->setTemplate('olegnax/product/view/cloudzoom.phtml');
		}
		return $this;
	}
}