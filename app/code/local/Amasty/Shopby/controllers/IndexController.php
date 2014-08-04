<?php
/**
* @copyright Amasty.
*/ 
class Amasty_Shopby_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        // init category
        $categoryId = (int) Mage::app()->getStore()->getRootCategoryId();
        if (!$categoryId) {
            $this->_forward('noRoute', 'index', 'cms');
            return;
        }

        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);
            
        Mage::register('current_category', $category); 
        Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($category->getId());  
          
        // need to prepare layer params
        try {
            Mage::dispatchEvent('catalog_controller_category_init_after', 
                array('category' => $category, 'controller_action' => $this));
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return;
        } 
        // observer can change value
        if (!$category->getId()){
            $this->_forward('noRoute', 'index', 'cms');
            return;
        }

        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        if ($helper->useSolr()) {
            Mage::register('_singleton/catalog/layer', Mage::getSingleton('enterprise_search/catalog_layer'));
        }
            
        $this->loadLayout();

        $this->checkAddRwdBlocks();

        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('checkout/session');  

        $this->renderLayout();
    }

    protected function checkAddRwdBlocks()
    {
        $package = Mage::getDesign()->getPackageName();
        if ($package != 'rwd') {
            return;
        }

        $productList = $this->getLayout()->getBlock('product_list');

        $nameAfter = $this->getLayout()->createBlock('core/text_list', 'product_list.name.after');
        $productList->setChild('name.after', $nameAfter);

        $after = $this->getLayout()->createBlock('core/text_list', 'product_list.after');
        $productList->setChild('after', $after);
    }
}