<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Collpur
 * @version    1.0.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Collpur_Block_Dealview extends AW_Collpur_Block_BaseDeal {
    const PRODUCT_PARAM = 'product';
    const DEAL_PARAM = 'deal_id';

    protected $_awcpStoreModel;
    protected $_currencyHelper;
    protected $_dealModel;
    protected $_baseDeal;
    protected $_cmsdeal;
    protected $_bridge;

    protected function _construct() {

        parent::_construct();

        if ($this->getCmsdeal()) {
            $this->_cmsdeal = $this->getCmsdeal();
        }

        $this->setTemplate('aw_collpur/deals/view.phtml');
        $this->_awcpStoreModel = Mage::app()->getStore();
        $this->_currencyHelper = Mage::helper('core');
        $this->_dealModel = Mage::getModel('collpur/deal');
        $this->_bridge = Mage::getBlockSingleton('collpur/deals');
        $this->_mode = 'product';
        $this->imageType = 'image';
    }

    protected function _prepareLayout() {

        if (!$this->getProduct(true)->isAvailable() && $this->_cmsdeal) {
            return;
        }

        $mageProduct = $this->_checkRegistry('product');
        $mageCurrentProduct = $this->_checkRegistry('current_product');

        $product = $this->getProduct();
        $layout = $this->getLayout();

        $this->_modifyCrumbs($layout, $this->_bridge, $this->_deal);

        Mage::register('product', $this->getProduct(), true);
        Mage::register('current_product', $this->getProduct(), true);

        $this->_addCanonicalLink($product, $layout);

        $optionsContainer = $product->getTypeId() == 'bundle' ? $this->_prepareBundleOptions($layout, $product) : $this->_prepareCommonOptions($layout, $product);
        $optionsContainer->setCmsDeal($this->_cmsdeal)->setMageProduct($mageProduct)->setMageCurrentProduct($mageCurrentProduct);
        $this->setChild('main_collpur_block', $optionsContainer);
        $addthisBlock = $layout->createBlock('core/template')->setTemplate('aw_collpur/social/addthis.phtml');
        $this->setChild('awgd_addthis', $addthisBlock);
    }

    private function _addCanonicalLink($product, $layout) {

        $layout->getBlock('head')->addLinkRel('canonical', $this->_bridge->getDealPageLink($this->_deal->getId()));
    }

    private function _checkRegistry($key) {

        if (Mage::registry($key)) {
            return Mage::registry($key);
        }

        return 0;
    }

    private function _prepareBundleOptions($layout, $product) {

        $js = $layout->createBlock('core/template', 'collpur_js')->setTemplate('catalog/product/view/options/js.phtml');
        $price = $layout->createBlock('catalog/product_view', 'product_price')->setTemplate('catalog/product/view/price_clone.phtml');
        $price->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/view/price.phtml');
        $options = $layout->createBlock('catalog/product_view_options', 'product_options')->setTemplate('catalog/product/view/options.phtml')
                        ->addOptionRenderer('text', 'catalog/product_view_options_type_text', 'catalog/product/view/options/type/text.phtml')
                        ->addOptionRenderer('select', 'catalog/product_view_options_type_select', 'catalog/product/view/options/type/select.phtml')
                        ->addOptionRenderer('date', 'catalog/product_view_options_type_date', 'catalog/product/view/options/type/date.phtml')
                        ->addOptionRenderer('file', 'catalog/product_view_options_type_file', 'catalog/product/view/options/type/file.phtml');
        $bundleOptionsBlock = $layout->createBlock('bundle/catalog_product_view_type_bundle', 'bundle_product_options')->setTemplate('bundle/catalog/product/view/type/bundle/options.phtml');
        $bundleOptionsBlock->addRenderer('checkbox', 'bundle/catalog_product_view_type_bundle_option_checkbox');
        $bundleOptionsBlock->addRenderer('select', 'bundle/catalog_product_view_type_bundle_option_select');
        $bundleOptionsBlock->addRenderer('multi', 'bundle/catalog_product_view_type_bundle_option_multi');
        $bundleOptionsBlock->addRenderer('radio', 'bundle/catalog_product_view_type_bundle_option_radio');
        $bundleConfig = $layout->createBlock('catalog/product_view', 'bundle_config_helper')->setTemplate('aw_collpur/config.phtml');
        $main = $layout->createBlock('bundle/catalog_product_view_type_bundle', 'main_collpur_block')->setTemplate('aw_collpur/options.phtml')
                        ->append($options)
                        ->append($bundleConfig)
                        ->append($bundleOptionsBlock);
        $main->append($js)->append($price);

        return $main;
    }

    private function _prepareCommonOptions($layout, $product) {

        $options = $layout->createBlock('catalog/product_view_options', 'product_options')
                        ->setTemplate('catalog/product/view/options.phtml')
                        ->addOptionRenderer('text', 'catalog/product_view_options_type_text', 'catalog/product/view/options/type/text.phtml')
                        ->addOptionRenderer('select', 'catalog/product_view_options_type_select', 'catalog/product/view/options/type/select.phtml')
                        ->addOptionRenderer('date', 'catalog/product_view_options_type_date', 'catalog/product/view/options/type/date.phtml')
                        ->addOptionRenderer('file', 'catalog/product_view_options_type_file', 'catalog/product/view/options/type/file.phtml');

        $price = $layout->createBlock('catalog/product_view', 'product_price')->setTemplate('catalog/product/view/price_clone.phtml');
        $js = $layout->createBlock('core/template', 'collpur_js')->setTemplate('catalog/product/view/options/js.phtml');

        if ($product->isConfigurable()) {
            $configurable = $layout->createBlock('catalog/product_view_type_configurable', 'product_configurable_options')->setTemplate('catalog/product/view/type/options/configurable.phtml');
            $configurableData = $layout->createBlock('catalog/product_view_type_configurable', 'product_type_data')->setTemplate('catalog/product/view/type/configurable.phtml');
        }
        if ($product->getTypeId() == 'downloadable') {
            $downloadable = $layout->createBlock('downloadable/catalog_product_links', 'product_downloadable_options')->setTemplate('downloadable/catalog/product/links.phtml');
            $downloadableData = $layout->createBlock('downloadable/catalog_product_view_type', 'product_type_data')->setTemplate('downloadable/catalog/product/type.phtml');
        }

        $main = $layout->createBlock('catalog/product_view', 'main_collpur_block')->setTemplate('aw_collpur/options.phtml');

        if ($this->getProduct()->isConfigurable()) {
            $main->append($configurableData);
            $main->append($configurable);
        }
        if ($this->getProduct()->getTypeId() == 'downloadable') {
            $main->append($downloadableData);
            $main->append($downloadable);
        }
        $main->append($js)->append($price)->append($options);

        return $main;
    }

    public function hasOptions() {
        if ($this->getProduct()->getTypeInstance(true)->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }

    protected function _toHtml() {

        return parent::_toHtml();
    }

    public function getDeal() {

        return $this->_deal;
    }

    public function getOriginalProduct() {

        $product = Mage::getModel('catalog/product')->load($this->_originalProductId);

        return $product;
    }

    public function getDealPricesSpare($orig, $deal) {

        $priceInfo = new Varien_Object();
        $price = $this->_currencyHelper->currency($deal->getPrice());
        $save = $this->_currencyHelper->currency($orig->getFinalPrice() - $deal->getPrice(), true, false);

        /* Avoide devision by zero */
        $discount = 0;
        if ($orig->getFinalPrice()) {
            $discount = ($orig->getFinalPrice() - $deal->getPrice()) / $orig->getFinalPrice() * 100;
        }

        $priceInfo->setPrice($price)
                ->setSaveAmount($save)
                ->setPercentDiscount(round($discount, 1));

        return $priceInfo;
    }

    public function getPurchasesCount($dealId) {

        return $this->_dealModel->load($dealId)->getPurchasesCount();
    }

    public function getPurchasesToReach($deal) {

        if (!$deal->getPurchasesLeft()) {
            return $deal->getQtyToReachDeal();
        }
        return $deal->getPurchasesLeft();
    }

    public function getSelfPageLink($deal, $link = false) {

        $pageLink = Mage::getUrl('deals/index/view',
            array(
                  '_store'  => Mage::app()->getStore()->getId(),
                  'id'      => Mage::app()->getRequest()->getParam('id'),
                  '_secure' => Mage::app()->getStore(true)->isCurrentlySecure()
            )
        );
        if ($link)
            return $pageLink;

        return $deal->getName() . ' : ' . $pageLink;
    }

    public function getProduct($init = false) {

        if ($this->_cmsdeal) {
            $deal = Mage::getModel('collpur/deal')->load($this->_cmsdeal);
        } else {
            $deal = Mage::getModel('collpur/deal')->load(Mage::app()->getRequest()->getParam('id'));
        }

        if ($init) {
            return $deal;
        }

        $this->_deal = $deal;
        $this->_originalProductId = $deal->getProductId();
        $product = Mage::getModel('catalog/product')->load($deal->getProductId())->setDeal($deal);
        $product->setData('price', $product->getDeal()->getPrice());
        $product->setData('final_price', $product->getDeal()->getPrice());

        $this->_modifyMeta($product, $deal);

        return $product;
    }

    public function isFeaturedMode() {

        if (Mage::app()->getRequest()->getParam('mode') == AW_Collpur_Helper_Deals::FEATURED) {
            return true;
        }
        return false;
    }

    private function _modifyMeta($product, $deal) {

        $product->setData('name', $this->processDealName($deal));
        $product->setData('meta_title', $this->processDealName($deal));
        $product->setdata('meta_description', $deal->getDescription());
    }

    protected function _hasOptionsWithPrice($product) {

        foreach ($product->getOptions() as $option) {
            foreach ($option->getValues() as $value) {
                if ($value->getPrice() > 0) {
                    return true;
                }
            }
        }

        return false;
    }

}