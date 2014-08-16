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


class AW_Collpur_Block_Customer_Dealview extends AW_Collpur_Block_Customer_Basedeal {

    protected $_rewriteResource;
    const EMPTY_STRING = '--';

    protected function _construct() {
      
        parent::_construct();
        $this->_rewriteResource = Mage::getResourceModel('collpur/rewrite');
    }

    protected function getPurchaseId() {
        if (!$this->getPurchaseIdParam()) {
            $purchaseId = (int) strip_tags(Mage::app()->getRequest()->getParam('purchase_id'));
            $this->setPurchaseIdParam($purchaseId);
        }
        return $this->getPurchaseIdParam();
    }

    public function getDealByPurchase($id=NULL) {

        if (!$this->getCustomerDeal()) {
            $this->setCustomerDeal(Mage::getModel('collpur/deal')->load($id));
        }

        return $this->getCustomerDeal();
    }

    public function getPricePaid($orderId) {
        $order = Mage::getModel('sales/order')->load($orderId);
        if(!$order->getId()) { return self::EMPTY_STRING; }
        $purchase = $this->getPurchase();
        $item = $order->getItemById($purchase->getOrderItemId());
        if(!$item->getId()) { return self::EMPTY_STRING; }
        return $this->_currencyHelper->currency($item->getBaseRowTotal());
    }

    public function getPurchaseCoupons($purchaseId) {

        return $this->getDealByPurchase($purchaseId)->collectCouponCodes($purchaseId);
    }

    public function getDealStatus() {

        $deal = $this->getDealByPurchase($this->getPurchase()->getDealId());

        if ($deal->getProgress() == AW_Collpur_Model_Source_Progress::PROGRESS_EXPIRED || $deal->isFailed()) {
            return $this->__('Failed');
        } elseif ($deal->getIsSuccess()) {
            return $this->__('Succeed');
        } else {
            return $this->__('Pending');
        }
    }

    public function getDealUrl() {
        $deal = $this->getDealByPurchase($this->getPurchase()->getDealId());
        $identifier = $this->_rewriteResource->loadByDealId($deal->getId(), Mage::app()->getStore()->getId());
        $prefix = Mage::getStoreConfig('catalog/seo/product_url_suffix');
        return Mage::getUrl("deals/", array('_secure' => Mage::app()->getStore(true)->isCurrentlySecure())) . "{$identifier}{$prefix}";
    }

    public function getPurchase() {

        if ($this->getPurchaseId()) {
            $purchase = Mage::getModel('collpur/dealpurchases')->load($this->getPurchaseId());
            if (!$purchase->getId())
                return false;
            return $purchase;
        }
        return false;
    }

    public function validateCustomer() {       
        if ($this->getPurchaseId()) {
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            return Mage::getModel('collpur/dealpurchases')->load($this->getPurchaseId())->getCustomerId() == $customerId;
        }
        return false;
    }

    public function getPurchaseCouponsCollection($purchaseId) {
        return
                Mage::getModel('collpur/coupon')
                ->getCollection()
                ->addFieldToFilter('purchase_id', $purchaseId);
    }

    protected function _toHtml() {
        /** Add coupons block */
         $couponsGrid = Mage::getBlockSingleton('collpur/customer_coupons');
         $this->setChild('awcp_coupons_grid',$couponsGrid);
         return parent::_toHtml();       
    }

}