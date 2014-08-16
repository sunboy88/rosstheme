<?php
class Amasty_Shopby_Block_Advanced extends Mage_Catalog_Block_Navigation
{
    public function drawOpenCategoryItem($category, $level = 0)
    {
        if ($this->_isExcluded($category->getId()) || !$category->getIsActive() || !$category->getIncludeInMenu()) {
            return '';
        }
        

        $cssClass = array(
            'amshopby-cat',
            'level' . $level
        );
        
        if ($this->_isCurrentCategory($category)) {
            $cssClass[] = 'active';
        }
        
        if ($this->isCategoryActive($category)) {
            $cssClass[] = 'parent';
        }

        if($category->hasChildren()) {
            $cssClass[] = 'has-child';
        }


        $productCount = '';
        if ($this->showProductCount()) {
            $productCount = $category->getProductCount();
            if ($productCount > 0) {
                $productCount = '(' . $productCount . ')';
            } else {
                $productCount = '';
            }
        }
        
        $html = array();
        $html[1] = '<a href="' . $this->getCategoryUrl($category) . '">' . $this->htmlEscape($category->getName()) . '</a>' . $productCount;
        
        $showAll   = Mage::getStoreConfig('amshopby/advanced_categories/show_all_categories');
        $showDepth = Mage::getStoreConfig('amshopby/advanced_categories/show_all_categories_depth');

        $hasChild = false;
        if (in_array($category->getId(), $this->getCurrentCategoryPath()) || ($showAll && $showDepth == 0) || ($showAll && $showDepth > $level + 1)) {
                
            $children = $this->_getCategoryCollection()->addIdFilter($category->getChildren());
            $this->_addCounts($children);
            $children = $this->asArray($children);

            if ($children && count($children) > 0) {
                $hasChild = true;
                $htmlChildren = '';
                foreach($children as $i => $child) {
                    $htmlChildren .= $this->drawOpenCategoryItem($child, $level + 1);
                }

                if($htmlChildren != '') {
                    $cssClass[] = 'expanded';
                    $html[2] = '<ul>' . $htmlChildren . '</ul>';
                }
            }
        }

        $html[0] = sprintf('<li class="%s">', implode(" ", $cssClass));
        $html[3] = '</li>';

        ksort($html);

        if ($category->getProductCount() || ($hasChild && $htmlChildren)) {
            $result = implode('', $html);
        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        $query = array(
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );
        $url = Mage::helper('amshopby/url')->getFullUrl($query, false, $category);
        return $url;
    }

    /**
     * I need an array with the index being continunig numbers, so
     * it's possible to check for the previous/next category
     *
     * @param mixed $collection
     *
     * @return array
     */
    public function asArray($collection)
    {
        $array = array();
        foreach ($collection as $item) {
            $array[] = $item;
        }
        return $array;
    }


    protected function _isCurrentCategory($category)
    {
        $cat = $this->getCurrentCategory();
        if ($cat) {
            return ($cat->getId() == $category->getId());
        }
        return false;
    }


    /**
     * Get catagories of current store, using the max depth setting for the vertical navigation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    public function getCategories()
    {
        /* @var $category Mage_Catalog_Model_Category */
        $category = Mage::getModel('catalog/category');
        $parent = Mage::registry('current_category');
        
        $startFrom = Mage::getStoreConfig('amshopby/advanced_categories/start_category');
        
        switch($startFrom) {            
            case Amasty_Shopby_Model_Source_Category_Start::START_CHILDREN:
                break;
            case Amasty_Shopby_Model_Source_Category_Start::START_CURRENT:
                if ($parent) {
                    $parent = $parent->getParentCategory();
                }
                break;
            case Amasty_Shopby_Model_Source_Category_Start::START_ROOT:
                $parent = $category->load(Mage::app()->getStore()->getRootCategoryId());
                break;
            default:
                $parent = $category->load(Mage::app()->getStore()->getRootCategoryId());
        }
        
        if (!$parent) {
            $parent = $category->load(Mage::app()->getStore()->getRootCategoryId());
        }
        $storeCategories = $this->_getCategoryCollection()->addIdFilter($parent->getChildren());
        return $storeCategories;
    }

    protected function _getCategoryCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getResourceModel('catalog/category_collection');

        $collection
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('all_children')
            ->addAttributeToSelect('is_anchor')
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('include_in_menu', 1)
            ->setOrder('position', 'asc')
            ->joinUrlRewrite();

        return $collection;
    }

    public function showProductCount()
    {
        return Mage::getStoreConfigFlag('amshopby/advanced_categories/display_product_count');
    }
    
    protected function _toHtml()
    {
        $html = '';
        
        $cats = $this->getCategories();
        $this->_addCounts($cats);

        $storeCategories = $this->asArray($cats);

        if (count($storeCategories) > 0) {
             foreach ($storeCategories as $c) {
                 if (!$this->_isExcluded($c->getId())) {
                    $html .= $this->drawOpenCategoryItem($c, 0);
                 }
             }
        }
        return $html;
    }

    protected function _addCounts($categories)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = clone Mage::getSingleton('catalog/layer')->getProductCollection();
        $select = $collection->getSelect();

        $part = $select->getPart(Varien_Db_Select::FROM);

        if (isset($part['cat_index'])) {
            $part['cat_index']['joinCondition'] = preg_replace('/cat_index.category_id=\'\d+\'/i', '1', $part['cat_index']['joinCondition']);
            $select->setPart(Varien_Db_Select::FROM, $part);
        }

        $collection->addCountToCategories($categories);
    }

    protected function _isExcluded($categoryId)
    {
        if (!$this->hasData('exclude_ids')) {
            $excludeIds = preg_replace('/[^\d,]+/', '', Mage::getStoreConfig('amshopby/general/exclude_cat'));
            $excludeIds = $excludeIds ? explode(',',  $excludeIds) : array();
            $this->setData('exclude_ids', $excludeIds);
        }
        $excludeIds = $this->getData('exclude_ids');
        if (in_array($categoryId, $excludeIds)) {
            return true;
        };

        if (!$this->hasData('include_ids')) {
            $includeIds = preg_replace('/[^\d,]+/', '', Mage::getStoreConfig('amshopby/general/include_cat'));
            $includeIds = $includeIds ? explode(',',  $includeIds) : array();
            $this->setData('include_ids', $includeIds);
        }
        $includeIds = $this->getData('include_ids');
        if ($includeIds && !in_array($categoryId, $includeIds)) {
            return true;
        };

        return false;
    }
}
