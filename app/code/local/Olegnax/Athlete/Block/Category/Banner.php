<?php
/**
 * Category banner block
 *
 */
class Olegnax_Athlete_Block_Category_Banner extends Mage_Core_Block_Template
{
	/**
	 * @var string
	 */
	private $_bannerHtml;

	const CACHE_TAG  = 'catalog_category_banner';

	protected function _construct()
	{
		$this->addData(array(
			'cache_lifetime' => 86400,
			'cache_tags'     => array(Olegnax_Athlete_Block_Category_Banner::CACHE_TAG),
		));
	}

	/**
	 * Get Key pieces for caching block content
	 *
	 * @return array
	 */
	public function getCacheKeyInfo()
	{
		return array(
			Olegnax_Athlete_Block_Category_Banner::CACHE_TAG,
			Mage::app()->getStore()->getId(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template'),
			$this->_getCurrentCategoryId(),
		);
	}

	protected function _getCurrentCategoryId()
	{
		if (!$_category = Mage::registry('current_category')) {
			return '';
		}
		return $_category->getId();
	}

	protected function _isCategoryBanner()
	{
		$category_block_id = 'athlete_category_'.$this->_getCurrentCategoryId();

		$category_block = Mage::getModel('cms/block')
			->setStoreId( Mage::app()->getStore()->getId() )
			->load($category_block_id);
		if($category_block->getIsActive()) {
			$this->_bannerHtml = $this->getLayout()->createBlock('cms/block')->setBlockId($category_block_id)->toHtml();
		}

	}

	/**
	 * check if category has static block
	 * and assing template if block exist
	 *
	 * @return $this|Mage_Core_Block_Abstract
	 */
	protected function _beforeToHtml()
	{
		$this->_isCategoryBanner();
		if ( !empty($this->_bannerHtml) ) {
			$this->setTemplate('olegnax/category/banner.phtml');
		}
		return $this;
	}

	public function getBannerHtml()
	{
		return $this->_bannerHtml;
	}
}