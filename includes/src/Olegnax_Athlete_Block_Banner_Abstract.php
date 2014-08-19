<?php
/**
 * Category banner block
 *
 */
class Olegnax_Athlete_Block_Banner_Abstract extends Mage_Core_Block_Template
{
	protected $_bannerHtml;
	protected $_module;
	protected $_controller;
	protected $_config;

	const CACHE_TAG  = 'athlete_banner';

	protected function _construct()
	{
		$this->_config = Mage::helper('athlete')->getCfg('', 'athlete_banners');
		$this->_module = Mage::app()->getRequest()->getModuleName();
		$this->_controller = Mage::app()->getRequest()->getControllerName();

		$this->addData(array(
			'cache_lifetime' => 86400,
			'cache_tags'     => array(self::CACHE_TAG),
		));
	}

	/**
	 * Get Key pieces for caching block content
	 *
	 * @return array
	 */
	public function getCacheKeyInfo()
	{
		$key = array(
			self::CACHE_TAG,
			Mage::app()->getStore()->getId(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template'),
			$this->_module,
			$this->_controller,
		);

		if ( $this->_module == 'catalog' ) {
			if ( $this->_controller == 'category' ) {
				$key[] = $this->_getCurrentCategoryId();
			}
			if ( $this->_controller == 'product' ) {
				$key[] = $this->_getCurrentProductId();
			}
		}

		return $key;
	}

	public function setBannerHtml( $html )
	{
		$this->_bannerHtml = $html;
	}

	public function getBannerHtml()
	{
		return $this->_bannerHtml;
	}

	protected function _beforeToHtml()
	{
		$this->_loadBanner();
		return $this;
	}

	protected function _loadBanner()
	{
	}

	protected function _loadStaticBlock( $block_id )
	{
		if (empty($block_id)) return;
		$block = Mage::getModel('cms/block')
			->setStoreId( Mage::app()->getStore()->getId() )
			->load($block_id);
		if($block->getIsActive()) {
			$this->setBannerHtml( $this->getLayout()->createBlock('cms/block')->setBlockId($block_id)->toHtml() );
		}
	}

	protected function _getCurrentCategoryId()
	{
		if (!$_item = Mage::registry('current_category')) {
			return '';
		}
		return $_item->getId();
	}

	protected function _getCurrentProductId()
	{
		if (!$_item = Mage::registry('current_product')) {
			return '';
		}
		return $_item->getId();
	}
}