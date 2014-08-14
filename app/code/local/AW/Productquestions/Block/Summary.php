<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productquestions
 * @version    1.5.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Productquestions_Block_Summary extends Mage_Core_Block_Template {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('productquestions/summary.phtml');
    }

    protected function _toHtml() {
        if (AW_Productquestions_Helper_Data::isModuleOutputDisabled())
            return '';

        $product = Mage::helper('productquestions')->getCurrentProduct(true);
        if (!($product instanceof Mage_Catalog_Model_Product))
            return '';

        $productId = $product->getId();

        $category = Mage::registry('current_category');
        if ($category instanceof Mage_Catalog_Model_Category)
            $categoryId = $category->getId();
        else
            $categoryId = false;

        $questionCount = Mage::getResourceModel('productquestions/productquestions_collection')
                ->addProductFilter($productId)
                ->addVisibilityFilter()
                ->addAnsweredFilter()
                ->addStoreFilter()
                ->getSize();

        $params = array('id' => $productId);
        if ($categoryId)
            $params['category'] = $categoryId;

        $suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix');

        /* if($urlKey = $product->getUrlKey())
          {
          $requestString = ltrim(Mage::app()->getFrontController()->getRequest()->getRequestString(), '/');

          $pqSuffix = $urlKey.$suffix;
          if($pqSuffix == substr($requestString, strlen($requestString)-strlen($pqSuffix)))
          {
          $requestString = substr($requestString, 0, strlen($requestString)-strlen($suffix));
          $this->setQuestionsPageUrl($this->getBaseUrl().$requestString.AW_Productquestions_Model_Urlrewrite::SEO_SUFFIX.$suffix);
          }
          } */

        if (Mage::getStoreConfig('productquestions/seo/enable_url_rewrites') && Mage::getModel('core/url_rewrite')->load($product->getId(), 'product_id')->getId()) {
            $productUrl = $product->getProductUrl();
            $fileExtentionPos = ($suffix == '') ? strlen($productUrl) : strrpos($productUrl, $suffix);
            $this->setQuestionsPageUrl(substr($productUrl, 0, $fileExtentionPos) . AW_Productquestions_Model_Urlrewrite::SEO_SUFFIX . $suffix);
        } else {
            $this->setQuestionsPageUrl(Mage::getUrl('productquestions/index/index/', $params));
        }

        $this->setQuestionCount($questionCount);

        return parent::_toHtml();
    }

}
