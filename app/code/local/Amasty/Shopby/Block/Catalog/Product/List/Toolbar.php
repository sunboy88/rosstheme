<?php
/**
 * @copyright   Copyright (c) 2010 Amasty
 */
class Amasty_Shopby_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    protected $_pagerAlias = 'product_list_toolbar_pager';

    public function getPagerUrl($params=array())
    {
        if ($this->skip())
            return parent::getPagerUrl($params);
            
        return Mage::helper('amshopby/url')->getFullUrl($params);
    }

    public function replacePager()
    {
        if ($this->skip()) {
            return;
        }

        /** @var Amasty_Shopby_Block_Catalog_Pager $newPager */
        $newPager = $this->getLayout()->createBlock('amshopby/catalog_pager');
        $newPager->setAvailableLimit($this->getAvailableLimit());

        $newPager->assign('_type', 'html')
            ->assign('_section', 'body');

        $this->setChild($this->_pagerAlias, $newPager);

        // Fix for limit = all and not set directly in request
        $newPager->setLimit($this->getLimit());

        $newPager->setupCollection();
        $newPager->handlePrevNextTags();
    }
    
    private function skip()
    {
        $r = Mage::app()->getRequest();
        if (in_array($r->getModuleName(), array('supermenu', 'supermenuadmin', 'catalogsearch','tag', 'catalogsale','catalognew')))
            return true;
            
        return false;
    }

    public function setChild($alias, $block)
    {
        if ($alias == $this->_pagerAlias && $this->getChild($this->_pagerAlias) instanceof Amasty_Shopby_Block_Catalog_Pager) {
            return $this;
        }
        return parent::setChild($alias, $block);
    }
}