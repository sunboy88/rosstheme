<?php

class Olegnax_Athlete_Block_Widget_Social extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
	/**
	 * Produces html
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$this->setTemplate('olegnax/widgets/social_icon.phtml');
		$html = '';
		$config = $this->getData('icon');
		if (empty($config)) {
			return $html;
		}
		$iconData = array(
			'icon' => $this->getData('icon'),
			'inverted' => $this->getData('inverted'),
			'href' => $this->getData('href'),
			'target' => $this->getData('target'),
		);

		$this->assign('iconData', $iconData);
		return parent::_toHtml();
	}
}