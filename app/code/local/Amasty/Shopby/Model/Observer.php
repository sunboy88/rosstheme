<?php
/**
 * @copyright   Copyright (c) 2009-2014 Amasty (http://www.amasty.com)
 */ 
class Amasty_Shopby_Model_Observer
{
    public function handleControllerFrontInitRouters($observer)
    {
        $observer->getEvent()->getFront()
            ->addRouter('amshopby', new Amasty_Shopby_Controller_Router());
    }
    
    public function handleCatalogControllerCategoryInitAfter($observer)
    {
        if (Mage::getStoreConfig('amshopby/seo/urls')) {
            $controller = $observer->getEvent()->getControllerAction();
            $cat = $observer->getEvent()->getCategory();

            if (!Mage::helper('amshopby/url')->saveParams($controller->getRequest())){
                if ($cat->getId()  == Mage::app()->getStore()->getRootCategoryId()){
                    $cat->setId(0);
                    return;
                }
                else {
                    Mage::helper('amshopby')->error404();
                }
            }

            if ($cat->getDisplayMode() == 'PAGE' && Mage::registry('amshopby_current_params')){
                $cat->setDisplayMode('PRODUCTS');
            }
        }

        Mage::helper('amshopby')->restrictMultipleSelection();
    }
    
    public function handleLayoutRender()
    {
        /** @var Mage_Core_Model_Layout $layout */
        $layout = Mage::getSingleton('core/layout');
        if (!$layout)
            return;
            
        $isAJAX = Mage::app()->getRequest()->getParam('is_ajax', false);
        $isAJAX = $isAJAX && Mage::app()->getRequest()->isXmlHttpRequest();
        if (!$isAJAX)
            return;
            
        $layout->removeOutputBlock('root');    
        Mage::app()->getFrontController()->getResponse()->setHeader('content-type', 'application/json');
            
        $page = $layout->getBlock('product_list');
        if (!$page){
            $page = $layout->getBlock('search_result_list');
        }

        if (!$page)
            return; 
            
        $blocks = array();
        foreach ($layout->getAllBlocks() as $b){
            if (!in_array($b->getNameInLayout(), array('amshopby.navleft','amshopby.navtop','amshopby.navright', 'amshopby.top', 'amfinder89'))){
                continue;
            }
            $b->setIsAjax(true);
            $html = $b->toHtml();
            if (!$html && false !== strpos($b->getBlockId(), 'amshopby-filters-'))
            {
                // compatibility with "shopper" theme
                // @see catalog/layer/view.phtml
                $queldorei_blocks = Mage::registry('queldorei_blocks');
                if ($queldorei_blocks AND !empty($queldorei_blocks['block_layered_nav']))
                {
                    $html = $queldorei_blocks['block_layered_nav'];
                }
            }
            $blocks[$b->getBlockId()] = $this->_removeAjaxParam($html);
        }
        
        if (!$blocks)
            return;

        $container = $layout->createBlock('core/template', 'amshopby_container');
        $container->setData('blocks', $blocks);
        $container->setData('page', $this->_removeAjaxParam($page->toHtml()));
        
        $layout->addOutputBlock('amshopby_container', 'toJson');
    }
    
    protected function _removeAjaxParam($html)
    {
        $sep = Mage::getStoreConfig('amshopby/seo/special_char');
        $html = str_replace('is' . $sep . 'ajax=1&amp;', '', $html);
        $html = str_replace('is' . $sep . 'ajax=1&', '', $html);
        $html = str_replace('is' . $sep . 'ajax=1', '', $html);
        
        $html = str_replace('___SID=U', '', $html);
        
        return $html;
    }

    public function handleBlockOutput($observer)
    {
        if (!Mage::getStoreConfigFlag('amshopby/block/ajax'))
            return;

        /* @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();

        if ($block instanceof Mage_Catalog_Block_Product_List) {
            $transport = $observer->getTransport();
            $html = $transport->getHtml();

            if (strpos($html, "amshopby-page-container") === FALSE){
                $html = '<div class="amshopby-page-container" id="amshopby-page-container">' .
                            $html .
                            '<div style="display:none" class="amshopby-overlay"><div></div></div>'.
                        '</div>';

                $transport->setHtml($html);
            }
        }
    }

    /**
     * Reset search engine if it is enabled for catalog navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentCatalogLayer(Varien_Event_Observer $observer)
    {
        if ($this->_getDataHelper()->useSolr()) {
            Mage::register('_singleton/catalog/layer', Mage::getSingleton('enterprise_search/catalog_layer'));
        }
    }

    /**
     * Reset search engine if it is enabled for search navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentSearchLayer(Varien_Event_Observer $observer)
    {
        if ($this->_getDataHelper()->useSolr()) {
            Mage::register('_singleton/catalogsearch/layer', Mage::getSingleton('enterprise_search/search_layer'));
        }
    }

    public function flushCache()
    {
        $this->_getDataHelper()->flushCache();
    }

    protected function _getDataHelper()
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        return $helper;
    }
}
