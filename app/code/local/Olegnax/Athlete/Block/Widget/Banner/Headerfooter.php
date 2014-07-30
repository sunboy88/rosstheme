<?php

class Olegnax_Athlete_Block_Widget_Banner_Headerfooter extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
	/**
	 * Produces html
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
        $this->setTemplate('olegnax/widgets/banners/headerfooter.phtml');
		$bannerData = array(
			'alias' => $this->getBlockAlias(),
			'blocks' => $this->getData('blocks'),
			'image_href' => $this->getData('image_href'),
			'image_alt' => $this->getData('image_alt'),
			'image_bg' => $this->getData('image_bg'),
			'image' => $this->getData('image'),
			'imageX2' => $this->getData('imageX2'),
            'block1' => array(
                'href' => $this->getData('block1_href'),
                'text' => $this->getData('block1_text'),
                'bg' => $this->getData('block1_bg'),
                'pattern' => $this->getData('block1_pattern'),
                'color' => $this->getData('block1_color'),
                'span_bg' => $this->getData('block1_span_bg'),
                'span_color' => $this->getData('block1_span_color'),
            ),
            'block2' => array(
                'href' => $this->getData('block2_href'),
                'text' => $this->getData('block2_text'),
                'bg' => $this->getData('block2_bg'),
	            'pattern' => $this->getData('block2_pattern'),
                'color' => $this->getData('block2_color'),
                'span_bg' => $this->getData('block2_span_bg'),
                'span_color' => $this->getData('block2_span_color'),
            ),
		);

        if ( empty($bannerData['image_href']) ) $bannerData['image_href'] = 'javascript:;';
        if ( empty($bannerData['block1']['href']) ) $bannerData['block1']['href'] = 'javascript:;';
        if ( empty($bannerData['block2']['href']) ) $bannerData['block2']['href'] = 'javascript:;';

        $this->assign('bannerData', $bannerData);
		return parent::_toHtml();
	}
}