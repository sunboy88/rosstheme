<?php
class Olegnax_Athlete_Block_Product_List_Featured extends Mage_Catalog_Block_Product_List
{
	protected $_cacheKeyArray;
	protected $_productsCount = null;
	protected $_blockTitle = null;

	const DEFAULT_PRODUCTS_COUNT = 6;

	protected function _construct()
	{
		$this->addData(array(
			'cache_lifetime' => 3600*24*30,
			'cache_tags'     => array('olegnax_athlete_product_list'),
		));
	}

	public function getCacheKeyInfo()
	{
		if (NULL === $this->_cacheKeyArray)
		{
			$this->_cacheKeyArray = array(
				Mage::app()->getStore()->getId(),
				Mage::getDesign()->getPackageName(),
				Mage::getDesign()->getTheme('template'),
				$this->getCategoryId(),
				$this->getProductsCount(),
				$this->getBlockTitle(),
				$this->getBlockTitleSize(),
				$this->getProductColumns(),
				$this->getIsRandom(),
				$this->getTemplate(),
			);
		}
		return $this->_cacheKeyArray;
	}

	/**
	 * apply parameters from cms block
	 *
	 * Available options
	 * category_id
	 * products_count
	 * block_title
	 * block_title_size
	 * product_columns
	 * is_random
	 *
	 * Retrieve loaded category collection
	 *
	 * @return Mage_Eav_Model_Entity_Collection_Abstract
	 */
	protected function _getProductCollection()
	{
		if (is_null($this->_productCollection)) {
			if ( $this->getCategoryId() ) {
				$category = Mage::getModel('catalog/category')->load($this->getCategoryId());
				$collection = $category->getProductCollection();
			} else {
				$collection = Mage::getResourceModel('catalog/product_collection');
			}
			Mage::getModel('catalog/layer')->prepareProductCollection($collection);
			$collection->addStoreFilter();
			$collection->addAttributeToSort('position');
			$isRandom = $this->getIsRandom();
			if ($isRandom)
				$collection->getSelect()->order('rand()');
			$productsCount = $this->getProductsCount();
			$collection->setPage(1, $productsCount)->load();
			$this->_productCollection = $collection;
		}
		return $this->_productCollection;
	}

	/**
	 * Get block title
	 *
	 * @return string
	 */
	public function getBlockTitle()
	{
		$this->_blockTitle = $this->getData('block_title');
		if (empty($this->_blockTitle)) {
			$this->_blockTitle = '';
		}
		return $this->_blockTitle;
	}

	/**
	 * Get number of products to be displayed
	 *
	 * @return int
	 */
	public function getProductsCount()
	{
		$this->_productsCount = $this->getData('products_count');
		if (empty($this->_productsCount)) {
			$this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
		}
		return $this->_productsCount;
	}

}
