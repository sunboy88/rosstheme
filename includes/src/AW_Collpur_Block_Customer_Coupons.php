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


class AW_Collpur_Block_Customer_Coupons extends AW_Collpur_Block_Customer_Basedeal {

    protected $_bridge;

    public function _construct() {

        parent::_construct();
        $this->setTemplate('aw_collpur/customer/coupons.phtml');
        $this->_bridge = Mage::getBlockSingleton('collpur/customer_dealview');
        $this->setAvailablePurchaseCoupons($this->getCustomerCoupons());
    }



      public function getCustomerCoupons() {

          if (isset($this->_data['available_purchase_coupons'])) {
                return $this->getAvailablePurchaseCoupons();
            }

            $collection = $this->_bridge->getPurchaseCouponsCollection($this->_bridge->getPurchase()->getId())->joinStatuses();

            $collection
                    ->getSelect()
                    ->where('main_table.deal_id IN (?)', $this->_bridge->getPurchase()->getDealId())
                    ->where('main_table.status != \'pending\'')
                    ->joinLeft(array('deals' => $collection->getTable('collpur/deal')), 'main_table.deal_id = deals.id', array('deals.product_name'))
                    ->joinLeft(
                            array('deal_purchases' => $collection->getTable('collpur/dealpurchases')),
                            'deal_purchases.id = main_table.purchase_id',
                            array('customer_name', 'order_id')
            );

            $this->setAvailablePurchaseCoupons($collection);
            return $this->getAvailablePurchaseCoupons();
    }

    protected function _prepareLayout() {

        $pager = $this->getLayout()->createBlock('page/html_pager', 'awcp_coupons_deals_pager');
        $pager->setLimitVarName('custlimit');
        $pager->setPageVarName('castvarname');
        $pager->setCollection($this->getCustomerCoupons());
        $this->setChild('awcp_coupons_deals_pager', $pager);
    }

    public function calculateCouponExpires() {

        $deal = $this->_bridge->getDealByPurchase($this->_bridge->getPurchase()->getDealId());
        if(!(int) $deal->getAvailableTo()) return '--';
        $purchaseId = $this->_bridge->getPurchase()->getId();
        $storeId = Mage::app()->getStore()->getId();
        return Mage::getModel('collpur/deal')->calculateCouponExpireDate($purchaseId, $deal, $storeId, false,true);
    }

    public function getPagerHtml() {
        return $this->getChildHtml('awcp_coupons_deals_pager');
    }

    public function isAvailable() {
        $deal = $this->_bridge->getDealByPurchase($this->_bridge->getPurchase()->getDealId());
        /* Coupons shoud be available and visible only after deal success */
        if($this->getCustomerCoupons()->getSize() && $deal->getEnableCoupons() == 1 && $deal->getIsSuccess()) return true; 
        return false;
    }

}