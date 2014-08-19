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


if (Mage::helper('collpur')->extensionEnabled('AW_Ascurl')) {
    class AW_Collpur_Mode_Catalog_Product_Url_Parent extends AW_Ascurl_Model_Catalog_Product_Url {}
}
else {
    class AW_Collpur_Mode_Catalog_Product_Url_Parent extends Mage_Catalog_Model_Product_Url {}
}
class AW_Collpur_Model_Catalog_Product_Url extends AW_Collpur_Mode_Catalog_Product_Url_Parent {

    public function getUrl(Mage_Catalog_Model_Product $product, $params = array()) {
        
        $deal = $this->productShouldNotHaveUrl($product);
        if ($deal === FALSE) {
            return parent::getUrl($product, $params);
        }
        
        $routePath = '';
        $routeParams = $params;

        $storeId = $product->getStoreId();
        if (isset($params['_ignore_category'])) {
            unset($params['_ignore_category']);
            $categoryId = null;
        } else {
            $categoryId = $product->getCategoryId() && !$product->getDoNotUseCategoryId() ? $product->getCategoryId() : null;
        }

        if (isset($routeParams['_store'])) {
            $storeId = Mage::app()->getStore($routeParams['_store'])->getId();
        }

        if ($storeId != Mage::app()->getStore()->getId()) {
            $routeParams['_store_to_url'] = true;
        }

        $routeParams['_direct'] = $this->rewriteProductUrlToDeal($deal);

        // reset cached URL instance GET query params
        if (!isset($routeParams['_query'])) {
            $routeParams['_query'] = array();
        }

        return $this->getUrlInstance()->setStore($storeId)
                ->getUrl($routePath, $routeParams);
    }

    public function productShouldNotHaveUrl($product) {

        /* Check custom option for deal id value */
        $deal = new Varien_Object();
        if ($product->getCustomOption('aw_collpur_dealidentity')) {
            $dealId = $product->getCustomOption('aw_collpur_dealidentity')->getValue();
            $deal = Mage::getModel('collpur/deal')->load($dealId);
        }

        if ($deal->getId() && $product->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE) {
            return $deal;
        }
        return false;
    }

    public function rewriteProductUrlToDeal($deal) {

        $identifier = Mage::getResourceModel('collpur/rewrite')->loadByDealId($deal->getId(), Mage::app()->getStore()->getId());
        $prefix = Mage::getStoreConfig('catalog/seo/product_url_suffix');
        return "deals/{$identifier}{$prefix}";
    }

}
