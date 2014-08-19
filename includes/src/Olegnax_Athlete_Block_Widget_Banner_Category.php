<?php

class Olegnax_Athlete_Block_Widget_Banner_Category extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
	/**
	 * Produces html
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$this->setTemplate('olegnax/widgets/banners/category.phtml');
		$bannerData = array(
			'alias' => $this->getBlockAlias(),
			'image_bg' => $this->getData('image_bg'),
			'image' => $this->getData('image'),
			'text' => $this->getData('text'),
			'text_bg' => $this->getData('text_bg'),
			'text_color' => $this->getData('text_color'),
			'link_text' => $this->getData('link_text'),
			'link_href' => $this->getData('link_href'),
			'link_bg' => $this->getData('link_bg'),
			'link_color' => $this->getData('link_color'),
			'link_icon' => $this->getData('link_icon'),
		);

		if ( empty($bannerData['link_icon']) ) {
			$bannerData['link_icon'] = Mage::helper('athlete')->getAppearanceCfg('content_banners/icon');
		}

		$this->assign('bannerData', $bannerData);
		return parent::_toHtml();
	}
}