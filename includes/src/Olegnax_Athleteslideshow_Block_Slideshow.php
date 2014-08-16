<?php
/**
 * @version   1.0 12.0.2012
 * @author    Olegnax http://www.olegnax.com <mail@olegnax.com>
 * @copyright Copyright (C) 2010 - 2012 Olegnax
 */

class Olegnax_Athleteslideshow_Block_Slideshow extends Mage_Core_Block_Template
{
	protected function _beforeToHtml()
	{
		$config = Mage::getStoreConfig('athleteslideshow', Mage::app()->getStore()->getId());
		if (Mage::helper('athleteslideshow')->isSlideshowEnabled()) {
			$this->setTemplate('olegnax/slideshow/' . $config['config']['slider'] . '.phtml');
		}

		return $this;
	}

	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	public function getSlideshow()
	{
		if (!$this->hasData('athleteslideshow')) {
			$this->setData('athleteslideshow', Mage::registry('athleteslideshow'));
		}
		return $this->getData('athleteslideshow');

	}

	public function getSlides()
	{
		$config = Mage::getStoreConfig('athleteslideshow', Mage::app()->getStore()->getId());
		if ( $config['config']['slider'] == 'athlete' ) {
			$model = Mage::getModel('athleteslideshow/athleteslideshow');
		} else {
			$model = Mage::getModel('athleteslideshow/athleterevolution');
		}
		$slides = $model->getCollection()
			->addStoreFilter(Mage::app()->getStore())
			->addFieldToSelect('*')
			->addFieldToFilter('status', 1)
			->setOrder('sort_order', 'asc');
		return $slides;
	}

}