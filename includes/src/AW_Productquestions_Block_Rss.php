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


class AW_Productquestions_Block_Rss extends Mage_Rss_Block_Abstract {

    protected function _construct() {
        $this->setCacheKey('rss_catalog_category_' . $this->getRequest()->getParam('id'));
        $this->setCacheLifetime(600);
    }

    protected function _toHtml() {
        $rssObj = Mage::getModel('rss/rss');


        $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($this->getRequest()->getParam('id'));

        $title = Mage::getStoreConfig('productquestions/rss/title');
        $vars = array(
            'product_name' => $product->getName(),
            'store_name' => Mage::helper('productquestions')->getStoreName(),
            'store_url' => $this->getBaseUrl(),
            'website_name' => Mage::app()->getWebsite()->getName(),
        );
        foreach ($vars as $key => $value)
            $title = str_replace('{' . $key . '}', $value, $title);

        $data = array(
            'title' => $title,
            'description' => $title,
            'link' => $this->getBaseUrl(),
            'charset' => 'UTF-8',
        );

        if (Mage::getStoreConfig('productquestions/rss/image'))
            $data['image'] = $this->getSkinUrl(Mage::getStoreConfig('productquestions/rss/image'));

        $rssObj->_addHeader($data);

        $collection = Mage::getResourceModel('productquestions/productquestions_collection')
                ->addStoreFilter()
                ->addVisibilityFilter()
                ->addAnsweredFilter()
                ->addProductFilter($this->getRequest()->getParam('id'))
                ->addLastXFilter(Mage::getStoreConfig('productquestions/rss/quantity'))
                ->applySorting(
                        AW_Productquestions_Model_Source_Question_Sorting::BY_DATE, AW_Productquestions_Model_Source_Question_Sorting::SORT_DESC)
                ->load();

        foreach ($collection->getItems() as $item) {
            $data = array(
                'title' => $this->__('From %s', $item->getQuestionAuthorName()),
                'author' => $item->getQuestionAuthorName(),
                'link' => $this->getUrl('productquestions/index/index', array('id' => $item->getProductId())) . '?qid=' . $item->getQuestionId() . '#' . $item->getQuestionId(),
                'description' => $this->__('<b>Question:</b><br/>%s<br/><br/><b>Answer:</b><br/>%s<br/><br/><br/>', $item->getQuestionText(), $item->getQuestionReplyText()),
                'lastUpdate' => strtotime($item->getQuestionDate()),
            );
            $rssObj->_addEntry($data);
        }

        return $rssObj->createRssXml();
    }

}
