<?php
/**
 * Catalog navigation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Olegnax_Athlete_Block_Navigation extends Mage_Core_Block_Template
{
	protected $_categoryInstance = null;

	/**
	 * Current category key
	 *
	 * @var string
	 */
	protected $_currentCategoryKey;

	/**
	 * Array of level position counters
	 *
	 * @var array
	 */
	protected $_itemLevelPositions = array();

	protected function _construct()
	{
		$this->addData(array(
			'cache_lifetime'    => false,
			'cache_tags'        => array(Mage_Catalog_Model_Category::CACHE_TAG, Mage_Core_Model_Store_Group::CACHE_TAG),
		));
	}

	/**
	 * Get Key pieces for caching block content
	 *
	 * @return array
	 */
	public function getCacheKeyInfo()
	{
		$shortCacheId = array(
			'CATALOG_NAVIGATION',
			Mage::app()->getStore()->getId(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template'),
			Mage::getSingleton('customer/session')->getCustomerGroupId(),
			'template' => $this->getTemplate(),
			'name' => $this->getNameInLayout(),
			$this->getCurrenCategoryKey()
		);
		$cacheId = $shortCacheId;

		$shortCacheId = array_values($shortCacheId);
		$shortCacheId = implode('|', $shortCacheId);
		$shortCacheId = md5($shortCacheId);

		$cacheId['category_path'] = $this->getCurrenCategoryKey();
		$cacheId['short_cache_id'] = $shortCacheId;

		return $cacheId;
	}

	/**
	 * Get current category key
	 *
	 * @return mixed
	 */
	public function getCurrenCategoryKey()
	{
		if (!$this->_currentCategoryKey) {
			$category = Mage::registry('current_category');
			if ($category) {
				$this->_currentCategoryKey = $category->getPath();
			} else {
				$this->_currentCategoryKey = Mage::app()->getStore()->getRootCategoryId();
			}
		}

		return $this->_currentCategoryKey;
	}

	/**
	 * Get catagories of current store
	 *
	 * @return Varien_Data_Tree_Node_Collection
	 */
	public function getStoreCategories()
	{
		$helper = Mage::helper('catalog/category');
		return $helper->getStoreCategories();
	}

	/**
	 * Retrieve child categories of current category
	 *
	 * @return Varien_Data_Tree_Node_Collection
	 */
	public function getCurrentChildCategories()
	{
		$layer = Mage::getSingleton('catalog/layer');
		$category   = $layer->getCurrentCategory();
		/* @var $category Mage_Catalog_Model_Category */
		$categories = $category->getChildrenCategories();
		$productCollection = Mage::getResourceModel('catalog/product_collection');
		$layer->prepareProductCollection($productCollection);
		$productCollection->addCountToCategories($categories);
		return $categories;
	}

	/**
	 * Checkin activity of category
	 *
	 * @param   Varien_Object $category
	 * @return  bool
	 */
	public function isCategoryActive($category)
	{
		if ($this->getCurrentCategory()) {
			return in_array($category->getId(), $this->getCurrentCategory()->getPathIds());
		}
		return false;
	}

	protected function _getCategoryInstance()
	{
		if (is_null($this->_categoryInstance)) {
			$this->_categoryInstance = Mage::getModel('catalog/category');
		}
		return $this->_categoryInstance;
	}

	/**
	 * Get url for category data
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @return string
	 */
	public function getCategoryUrl($category)
	{
		if ($category instanceof Mage_Catalog_Model_Category) {
			$url = $category->getUrl();
		} else {
			$url = $this->_getCategoryInstance()
				->setData($category->getData())
				->getUrl();
		}

		return $url;
	}

	/**
	 * Return item position representation in menu tree
	 *
	 * @param int $level
	 * @return string
	 */
	protected function _getItemPosition($level)
	{
		if ($level == 0) {
			$zeroLevelPosition = isset($this->_itemLevelPositions[$level]) ? $this->_itemLevelPositions[$level] + 1 : 1;
			$this->_itemLevelPositions = array();
			$this->_itemLevelPositions[$level] = $zeroLevelPosition;
		} elseif (isset($this->_itemLevelPositions[$level])) {
			$this->_itemLevelPositions[$level]++;
		} else {
			$this->_itemLevelPositions[$level] = 1;
		}

		$position = array();
		for($i = 0; $i <= $level; $i++) {
			if (isset($this->_itemLevelPositions[$i])) {
				$position[] = $this->_itemLevelPositions[$i];
			}
		}
		return implode('-', $position);
	}

	/**
	 * Render category to html
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @param int Nesting level number
	 * @param boolean Whether ot not this item is last, affects list item class
	 * @param boolean Whether ot not this item is first, affects list item class
	 * @param boolean Whether ot not this item is outermost, affects list item class
	 * @param string Extra class of outermost list items
	 * @param string If specified wraps children list in div with this class
	 * @param boolean Whether ot not to add on* attributes to list item
	 * @return string
	 */
	protected function _renderCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false,
	                                               $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false)
	{
		if (!$category->getIsActive()) {
			return '';
		}
		$html = array();

		// get all children
		if (Mage::helper('catalog/category_flat')->isEnabled()) {
			$children = (array)$category->getChildrenNodes();
			$childrenCount = count($children);
		} else {
			$children = $category->getChildren();
			$childrenCount = $children->count();
		}
		$hasChildren = ($children && $childrenCount);

		// select active children
		$activeChildren = array();
		foreach ($children as $child) {
			if ($child->getIsActive()) {
				$activeChildren[] = $child;
			}
		}
		$activeChildrenCount = count($activeChildren);
		$hasActiveChildren = ($activeChildrenCount > 0);

		// prepare list item html classes
		$classes = array();
		$classes[] = 'level' . $level;
		$classes[] = 'nav-' . $this->_getItemPosition($level);
		if ($this->isCategoryActive($category)) {
			$classes[] = 'active';
		}
		$linkClass = '';
		if ($isOutermost && $outermostItemClass) {
			$classes[] = $outermostItemClass;
			$linkClass = ' class="'.$outermostItemClass.'"';
		}
		if ($isFirst) {
			$classes[] = 'first';
		}
		if ($isLast) {
			$classes[] = 'last';
		}
		if ($hasActiveChildren) {
			$classes[] = 'parent';
		}

		// prepare list item attributes
		$attributes = array();
		if (count($classes) > 0) {
			$attributes['class'] = implode(' ', $classes);
		}
		if ($hasActiveChildren && !$noEventAttributes) {
			$attributes['onmouseover'] = 'toggleMenu(this,1)';
			$attributes['onmouseout'] = 'toggleMenu(this,0)';
		}

		// assemble list item with attributes
		$htmlLi = '<li';
		foreach ($attributes as $attrName => $attrValue) {
			$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
		}
		$htmlLi .= '>';
		$html[] = $htmlLi;

		$html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
		$html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
		$html[] = '</a>';

		// render children
		$htmlChildren = '';
		$j = 0;
		foreach ($activeChildren as $child) {
			$htmlChildren .= $this->_renderCategoryMenuItemHtml(
				$child,
				($level + 1),
				($j == $activeChildrenCount - 1),
				($j == 0),
				false,
				$outermostItemClass,
				$childrenWrapClass,
				$noEventAttributes
			);
			$j++;
		}
		if (!empty($htmlChildren)) {
			if ($childrenWrapClass) {
				$html[] = '<div class="' . $childrenWrapClass . '">';
			}
			$html[] = '<ul class="level' . $level . '">';
			$html[] = $htmlChildren;
			$html[] = '</ul>';
			if ($childrenWrapClass) {
				$html[] = '</div>';
			}
		}

		$html[] = '</li>';

		$html = implode("\n", $html);
		return $html;
	}

	/**
	 * Render category to html
	 *
	 * @deprecated deprecated after 1.4
	 * @param Mage_Catalog_Model_Category $category
	 * @param int Nesting level number
	 * @param boolean Whether ot not this item is last, affects list item class
	 * @return string
	 */
	public function drawItem($category, $level = 0, $last = false)
	{
		return $this->_renderCategoryMenuItemHtml($category, $level, $last);
	}

	/**
	 * Enter description here...
	 *
	 * @return Mage_Catalog_Model_Category
	 */
	public function getCurrentCategory()
	{
		if (Mage::getSingleton('catalog/layer')) {
			return Mage::getSingleton('catalog/layer')->getCurrentCategory();
		}
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @return string
	 */
	public function getCurrentCategoryPath()
	{
		if ($this->getCurrentCategory()) {
			return explode(',', $this->getCurrentCategory()->getPathInStore());
		}
		return array();
	}

	/**
	 * Enter description here...
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @return string
	 */
	public function drawOpenCategoryItem($category) {
		$html = '';
		if (!$category->getIsActive()) {
			return $html;
		}

		$html.= '<li';

		if ($this->isCategoryActive($category)) {
			$html.= ' class="active"';
		}

		$html.= '>'."\n";
		$html.= '<a href="'.$this->getCategoryUrl($category).'"><span>'.$this->htmlEscape($category->getName()).'</span></a>'."\n";

		if (in_array($category->getId(), $this->getCurrentCategoryPath())){
			$children = $category->getChildren();
			$hasChildren = $children && $children->count();

			if ($hasChildren) {
				$htmlChildren = '';
				foreach ($children as $child) {
					$htmlChildren.= $this->drawOpenCategoryItem($child);
				}

				if (!empty($htmlChildren)) {
					$html.= '<ul>'."\n"
						.$htmlChildren
						.'</ul>';
				}
			}
		}
		$html.= '</li>'."\n";
		return $html;
	}

	/**
	 * Render category to html
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @param int Nesting level number
	 * @param boolean Whether ot not this item is last, affects list item class
	 * @param boolean Whether ot not this item is first, affects list item class
	 * @param boolean Whether ot not this item is outermost, affects list item class
	 * @param string Extra class of outermost list items
	 * @param string If specified wraps children list in div with this class
	 * @param boolean Whether ot not to add on* attributes to list item
	 * @return string
	 */
	protected function _renderAthleteCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false,
	                                               $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false)
	{
		if (!$category->getIsActive()) {
			return '';
		}
		$html = array();

		// get all children
		if (Mage::helper('catalog/category_flat')->isEnabled()) {
			$children = (array)$category->getChildrenNodes();
			$childrenCount = count($children);
		} else {
			$children = $category->getChildren();
			$childrenCount = $children->count();
		}
		$hasChildren = ($children && $childrenCount);

		// select active children
		$activeChildren = array();
		foreach ($children as $child) {
			if ($child->getIsActive()) {
				$activeChildren[] = $child;
			}
		}
		$activeChildrenCount = count($activeChildren);
		$hasActiveChildren = ($activeChildrenCount > 0);

		// prepare list item html classes
		$classes = array();
		$classes[] = 'level' . $level;
		$classes[] = 'nav-' . $this->_getItemPosition($level);
		if ($this->isCategoryActive($category)) {
			$classes[] = 'active';
		}
		$linkClass = '';
		if ($isOutermost && $outermostItemClass) {
			$classes[] = $outermostItemClass;
			$linkClass = ' class="' . $outermostItemClass . '"';
		}
		if ($isFirst) {
			$classes[] = 'first';
		}
		if ($isLast) {
			$classes[] = 'last';
		}
		if ($hasActiveChildren) {
			$classes[] = 'parent';
		}

		// prepare list item attributes
		$attributes = array();
		if (count($classes) > 0) {
			$attributes['class'] = implode(' ', $classes);
		}
		if ($hasActiveChildren && !$noEventAttributes) {
			$attributes['onmouseover'] = 'toggleMenu(this,1)';
			$attributes['onmouseout'] = 'toggleMenu(this,0)';
		}

		// assemble list item with attributes
		$htmlLi = '<li';
		foreach ($attributes as $attrName => $attrValue) {
			$htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
		}
		$htmlLi .= '>';
		$html[] = $htmlLi;

		$html[] = '<a href="' . $this->getCategoryUrl($category) . '"' . $linkClass . '>';
		$html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
		$html[] = '</a>';

		if ($level == 0) {
			//get category description
			$ca = Mage::getModel('catalog/category')->load($category->getId());
			$description = $ca->getDescription();
			if (empty($description) || !Mage::helper('athlete')->getCfg('header/show_description')) {
				$columns = 4;
			} else {
				$columns = 2;
			}
			$columnItemsNum = array_fill(0, $columns, floor($activeChildrenCount / $columns));
			if ($activeChildrenCount % $columns > 0) {
				for ($i = 0; $i < ($activeChildrenCount % $columns); $i++) {
					$columnItemsNum[$i]++;
				}
			}
			$this->_columnHtml = array();
		}

		// render children
		$htmlChildren = '';
		$j = 0; //child index
		$i = 0; //column index
		$itemsCount = $activeChildrenCount;
		if (isset($columnItemsNum[$i])) {
			$itemsCount = $columnItemsNum[$i];
		}
		foreach ($activeChildren as $child) {

			if ($level == 0) {
				$isLast = (($j + 1) == $itemsCount || $j == $activeChildrenCount - 1);
				if ($isLast) {
					$i++;
					if (isset($columnItemsNum[$i])) {
						$itemsCount += $columnItemsNum[$i];
					}
				}
			} else {
				$isLast = ($j == $activeChildrenCount - 1);
			}

			$childHtml = $this->_renderAthleteCategoryMenuItemHtml(
				$child,
				($level + 1),
				$isLast,
				($j == 0),
				false,
				$outermostItemClass,
				$childrenWrapClass,
				$noEventAttributes
			);
			if ($level == 0) {
				//to fix Notice: Indirect modification of overloaded property
				$this->_columnHtml += array(count($this->_columnHtml) => $childHtml);
			} else {
				$htmlChildren .= $childHtml;
			}
			$j++;
		}

		if ($level == 0 && $this->_columnHtml) {
			$i = 0;
			foreach ($columnItemsNum as $columnNum) {
				$chunk = array_slice($this->_columnHtml, $i, $columnNum);
				$i += $columnNum;
				$htmlChildren .= '<li ' . (count($this->_columnHtml) == $i ? 'class="last"' : '') . '><ol>';
				foreach ($chunk as $item) {
					$htmlChildren .= $item;
				}
				$htmlChildren .= '</ol></li>';
			}
		}
		if (!empty($description) && !empty($htmlChildren) && Mage::helper('athlete')->getCfg('header/show_description')) {
			$htmlChildren .= '<li class="menu-category-description clearfix">' . $description;
			$htmlChildren .= '<p><button class="button" onclick="window.location=\'' . $this->getCategoryUrl($category) . '\'"><span><span>' . $this->__('learn more') . '</span></span></button></p>';
			$htmlChildren .= '</li>';
		}

		if (!empty($htmlChildren)) {
			if ($childrenWrapClass) {
				$html[] = '<div class="' . $childrenWrapClass . '">';
			}
			$html[] = '<ul class="level' . $level . '">';
			$html[] = $htmlChildren;
			$html[] = '</ul>';
			if ($childrenWrapClass) {
				$html[] = '</div>';
			}
		}

		$html[] = '</li>';

		$html = implode("\n", $html);
		return $html;
	}

	/**
	 * Render categories menu in HTML
	 *
	 * @param int Level number for list item class to start from
	 * @param string Extra class of outermost list items
	 * @param string If specified wraps children list in div with this class
	 * @return string
	 */
	public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
	{
		$activeCategories = array();
		foreach ($this->getStoreCategories() as $child) {
			if ($child->getIsActive()) {
				$activeCategories[] = $child;
			}
		}
		$activeCategoriesCount = count($activeCategories);
		$hasActiveCategoriesCount = ($activeCategoriesCount > 0);

		if (!$hasActiveCategoriesCount) {
			return '';
		}
		$render = Mage::helper('athlete')->getCfg('header/navigation');
		switch($render){
			case 'athlete':
				$render = '_renderAthleteCategoryMenuItemHtml';
				break;
			default:
				$render = '_renderCategoryMenuItemHtml';
		}

		$html = '';
		$j = 0;
		foreach ($activeCategories as $category) {
			$html .= $this->$render(
				$category,
				$level,
				($j == $activeCategoriesCount - 1),
				($j == 0),
				true,
				$outermostItemClass,
				$childrenWrapClass,
				true
			);
			$j++;
		}

		return $html;
	}

}
