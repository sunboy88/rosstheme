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


class AW_Collpur_Model_Deal extends Mage_Core_Model_Abstract {
    const STATE_OPEN = 1;
    const STATE_CLOSED = 2;
    const STATE_ARCHIVED = 3;

    public function _construct() {
        parent::_construct();
        $this->_init('collpur/deal');
    }

    public function getProduct() {
        return Mage::getModel('catalog/product')->load($this->getData('product_id'));
    }

    public function getDealName() {
        return $this->getData('name') ? $this->getData('name') : $this->getData('product_name');
    }

    public function dealProductExists($deal) {
        if (!Mage::getModel('catalog/product')->load($deal->getProductId())->getId()) {
            return false;
        }
        return true;
    }

    /**
     * Check success
     */
    public function checkSuccess() {
        if ($this->getData('qty_to_reach_deal') <= $this->getPurchasesCount()) {
            $this->setData('is_success', true);
        } else {
            $this->setData('is_success', false);
        }
        return $this;
    }

    public function checkAutoClose() {
        if ($this->getData('auto_close') && $this->getData('is_success')) {
            $this->setData('close_state', self::STATE_CLOSED);
            $this->setData('process_additional_states', 1);
        }
        return $this;
    }

    public function getPurchasesCount() {
        $dealPurchases =
                        Mage::getModel('collpur/dealpurchases')
                        ->getCollection()
                        ->addFieldToFilter('deal_id', $this->getId());
        $purchasesCount = 0;
        foreach ($dealPurchases as $dealPurchase) {
            $purchasesCount += $dealPurchase->getData('qty_purchased');
        }
        return $purchasesCount;
    }

    public function processOrderItem($orderItem, $errors=true) {

        if ($errors) {
            if (!$this->isAvailable()) {
                throw new Mage_Core_Exception(Mage::helper('collpur')->__('Deal #%s is not available more', $this->getId()));
            }
        }

        $order = $orderItem->getOrder();
        $purchase = Mage::getModel('collpur/dealpurchases')->loadByOrderItemId($orderItem->getId());

        $qtyToAdd = $orderItem->getData('qty_invoiced') - $orderItem->getData('qty_refunded');
        $afterPurchaseQty = $this->getPurchasesCount() - $purchase->getData('qty_purchased') + $qtyToAdd;

        if ($errors) {
            $this->validateRequestedQty($afterPurchaseQty);
        }

        /* Process specific new purchase actions */
        if (!$purchase->getId()) {
            $purchase->setPurchaseDateTime(Mage::getModel('core/date')->gmtDate());
            if (!Mage::getStoreConfig(AW_Collpur_Helper_Notifications::NOTIFICATIONS_ON, $order->getStoreId())) {
                $purchase->setIsSuccessedFlag(2);
            }
            /* Calculate shipping amount separetaly for order item */
            $purchase->setData('shipping_amount', $this->calculateShippingAmount($orderItem));
            $purchase->setData('qty_ordered', $orderItem->getQtyOrdered());
        }

        $purchase
                ->setData('qty_purchased', $qtyToAdd)
                ->setData('order_id', $order->getId())
                ->setData('order_item_id', $orderItem->getId())
                ->setData('deal_id', $this->getId())
                ->setData('customer_name', $order->getCustomerName())
                ->setData('customer_id', $order->getCustomerId())
                ->save();

        $purchase->connectWithFreeCoupons();
        $this->checkPurchasesCount()->checkSuccess()->checkAutoClose()->save();

        /* If ->checkAutoClose(), set this flag to process downloadable links */
        if ($this->getData('process_additional_states')) {
            $this->additionalProductProcessing();
        }


        /* Send email notifications right away if deal already successed and processed by cron */
        if ($this->getIsSuccess() && $this->getIsSuccessedFlag()) {
            AW_Collpur_Model_Cron::_processSuccessedDeals();
        }
    }

    public function calculateShippingAmount($orderItem) {

        $shippingAmount = 0;
        // If order item has no order or related product return 0
        if (!is_object($orderItem->getOrder()) || !is_object($orderItem->getProduct())) {
            return $shippingAmount;
        }

        if (!$orderItem->getOrder()->getIsVirtual() && !$orderItem->getProduct()->isVirtual()) {

            $siQuoteItemId = $orderItem->getQuoteItemId();
            if (Mage::app()->getRequest()->getControllerName() == 'multishipping') {
                $addressItem = Mage::getModel('sales/quote_address_item')->load($orderItem->getQuoteItemId());
                $siQuoteItemId = $addressItem->getQuoteItemId();
            }

            $siQuoteItem = Mage::getModel('sales/quote_item')->load($siQuoteItemId);
            $quoteId = $siQuoteItem->getQuoteId();
            $quote = Mage::getModel('sales/quote')->load($quoteId);

            $addressCollection = Mage::getModel('sales/quote_address')->getCollection()
                            ->addFieldToFilter('address_type', array('eq' => 'shipping'))
                            ->addFieldToFilter('quote_id', array('eq' => $quoteId));

            $address = $addressCollection->getFirstItem();

            $address->setQuote($quote);
            $siQuoteItem->setQuote($quote);

            try {
                if ($address->requestShippingRates($siQuoteItem)) {
                    $shippingAmount = $siQuoteItem->getBaseShippingAmount();
                }
            } catch (Exception $e) {
                Mage::log('Failed to calculate shippment for order item ' . $orderItem->getId());
                return $shippingAmount;
            }
        }

        return $shippingAmount;
    }

    public function collectCouponCodes($id) {

        $coupons = array();
        $collection = Mage::getModel('collpur/coupon')->getCollection()->addFieldToFilter('purchase_id', array('IN' => array($id)));
        foreach ($collection as $coupon) {
            $coupons[] = $coupon->getCouponCode();
        }
        if (!empty($coupons))
            return implode('<br />', $coupons);
        return NULL;
    }

    public function qtyIsNotAllowed($qty = 0) {
        if ($this->getData('auto_close') && (($this->getPurchasesCount() + $qty) > $this->getData('qty_to_reach_deal'))) {
            return true;
        } else if (($this->getPurchasesCount() + $qty > $this->getMaximumAllowedPurchases()) && $this->getMaximumAllowedPurchases()) {
            return true;
        }
        return false;
    }

    public function validateRequestedQty($qty = 0)
    {
        if (($this->getPurchasesCount() + $qty > $this->getMaximumAllowedPurchases())
            && $this->getMaximumAllowedPurchases()
        ) {
            throw new Mage_Core_Exception(
                Mage::helper('collpur')->__(
                    'Maximum allowed purchases %s',
                    $this->getMaximumAllowedPurchases() - $this->getPurchasesCount()
                )
            );
            return false;
        }

        if ($this->getData('auto_close')
            && (($this->getPurchasesCount() + $qty) > $this->getData('qty_to_reach_deal'))
        ) {
            throw new Mage_Core_Exception(
                Mage::helper('collpur')->__(
                    'Qty is not available, available qty: %s',
                    $this->getData('qty_to_reach_deal') - $this->getPurchasesCount()
                )
            );
            return false;
        }
        return true;
    }

    public function calculateCouponExpireDate($id, $deal, $storeId, $processCollection = true, $toLimit = false) {

        $days = (int) $deal->getCouponExpireAfterDays();

        if (!is_int($days) || !$deal->getAvailableTo()) {
            return NULL;
        }

        $dateHelper = Mage::getModel('core/date');
        $localeHelper = Mage::getModel('core/locale');

        if ($processCollection) {
            $collection = Mage::getModel('collpur/coupon')->getCollection()->addFieldToFilter('purchase_id', array('IN' => array($id)));
            if ($collection->count() == 0)
                return;
            foreach ($collection as $coupon) {
                $coupon->setCouponDeliveryDatetime($dateHelper->gmtDate())->save();
            }
        }
        if ($days === 0)
            return NULL;

        if ($toLimit) {
            $dateAfterStamp = AW_Collpur_Helper_Data::getGmtTimestamp($deal->getAvailableTo(), false, $days);
        } else {
            $dateAfterStamp = AW_Collpur_Helper_Data::getGmtTimestamp(true, true);
        }
        return $localeHelper->storeDate($storeId, $dateAfterStamp, true)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }

    public function checkPurchasesCount() {

        $purchasesLeft = $this->getData('qty_to_reach_deal') - $this->getPurchasesCount();
        $this->setData('purchases_left', $purchasesLeft > 0 ? $purchasesLeft : 0);
        return $this;
    }

    public function reopen() {

        /* Continue with deal */
        if ($this->getData('is_success')) {
            $this->setData('close_state', self::STATE_OPEN)->save();
        } else {
            /* Renew deal */
            $this
                    ->setData('close_state', self::STATE_OPEN)
                    ->setData('sent_before_flag', 0)
                    ->setData('expired_flag', 0)
                    ->setData('is_success', 0)
                    ->setData('is_successed_flag', 0)
                    ->setData('purchases_left', $this->getData('qty_to_reach_deal'))
                    ->save();
        }
        // For integration with Order Tags 1.1 version
        Mage::dispatchEvent('aw_collpur_deal_status_changed', array('deal' => $this));
    }

    public function additionalProductProcessing() {
        if ($this->getProduct()->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {

            $purchasedLinks = Mage::getResourceModel('downloadable/link_purchased_item_collection')
                            ->addFieldToFilter('order_item_id', array("in" => $this->_loadOrderItemIds()));


            foreach ($purchasedLinks as $link) {
                $orderItem = Mage::getModel('sales/order_item')->load($link->getOrderItemId());
                $buyRequest = Mage::helper('collpur')->getBuyRequest($orderItem); //$this->_getBuyRequest($orderItem);
                if ($buyRequest->getData('deal_id')) {
                    if ($this->isClosed() && $this->getData('is_success')) {
                        $link->setData('status', Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE)->save();
                    } else {
                        $link->setData('status', Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING)->save();
                    }
                }
            }
        }
        return $this;
    }

    public function isOpen() {
        return $this->getData('close_state') == self::STATE_OPEN;
    }

    public function isClosed() {
        return $this->getData('close_state') == self::STATE_CLOSED;
    }

    public function isArchived() {
        return $this->getData('close_state') == self::STATE_ARCHIVED;
    }

    public function isFeatured() {
        return $this->getData('is_featured') == '1';
    }

    public function isFailed() {
        return ($this->getData('close_state') == self::STATE_CLOSED) && (!$this->getData('is_success'));
    }

    public function isNotRunning() {

        $currentTime = Mage::getModel('core/date')->gmtTimestamp();
        $timeFrom = AW_Collpur_Helper_Data::getGmtTimestamp($this->getData('available_from'));

        if ($this->isOpen() && ($currentTime < $timeFrom))
            return true;

        return false;
    }

    public function isRunning() {

        $currentTime = Mage::getModel('core/date')->gmtTimestamp();
        $timeFrom = AW_Collpur_Helper_Data::getGmtTimestamp($this->getData('available_from'));
        $timeTo = AW_Collpur_Helper_Data::getGmtTimestamp($this->getData('available_to'));

        if ($this->isOpen() && (!$this->getData('available_from') || $timeFrom < $currentTime) && (!$this->getData('available_to') || $timeTo > $currentTime)) {
            return true;
        }
        return false;
    }

    public function getRandomFeaturedId() {

        $featured = $this->getCollection()
                        ->addIsActiveFilter()
                        ->addFeaturedFilter()
                        ->getActiveDeals()
                        ->getAllIds();

        if ($featured) {
            return $featured[array_rand($featured, 1)];
        }

        return false;
    }

    public function getMenuSize($section) {
        $base = $this->getCollection()->addIsActiveFilter();
        if ($section == AW_Collpur_Helper_Deals::RUNNING) {
            return $base->getActiveDeals()->getSize();
        } else if ($section == AW_Collpur_Helper_Deals::NOT_RUNNING) {
            return $base->getFutureDeals()->getSize();
        } else if ($section == AW_Collpur_Helper_Deals::CLOSED) {
            return $base->getClosedDeals()->getSize();
        } else if ($section == AW_Collpur_Helper_Deals::FEATURED) {
            return $this->getRandomFeaturedId();
        }
        return false;
    }

    public function getProgress() {

        $currentTime = Mage::getModel('core/date')->gmtTimestamp();
        $timeFrom = AW_Collpur_Helper_Data::getGmtTimestamp($this->getData('available_from'));
        $timeTo = AW_Collpur_Helper_Data::getGmtTimestamp($this->getData('available_to'));

        $progress = AW_Collpur_Model_Source_Progress::PROGRESS_RUNNING;
        if ($this->isOpen() && ($currentTime < $timeFrom)) {
            $progress = AW_Collpur_Model_Source_Progress::PROGRESS_NOT_RUNNING;
        } elseif ($this->isOpen() && ($currentTime > $timeTo) && $timeTo) {
            $progress = AW_Collpur_Model_Source_Progress::PROGRESS_EXPIRED;
        } elseif ($this->isClosed()) {
            $progress = AW_Collpur_Model_Source_Progress::PROGRESS_CLOSED;
        } elseif ($this->isArchived()) {
            $progress = AW_Collpur_Model_Source_Progress::PROGRESS_ARCHIVED;
        }
        return $progress;
    }

    public function isAvailable() {
        $coreDate = Mage::getModel('core/date');
        $currentTimestamp = $coreDate->gmtTimestamp();

        $availableFrom = $this->getData('available_from') ? $this->getData('available_from') : null;
        $fromAvailability = $availableFrom == null || $currentTimestamp >= AW_Collpur_Helper_Data::getGmtTimestamp($availableFrom);

        $availableTo = $this->getData('available_to') ? $this->getData('available_to') : null;
        $toAvailability = $availableTo == null || $currentTimestamp <= AW_Collpur_Helper_Data::getGmtTimestamp($availableTo);

        return $this->getData('is_active') && $this->isOpen() && $fromAvailability && $toAvailability;
    }

    public function isAllowed() {
        $config = AW_Collpur_Model_Source_Menus::getMenusArray(false);
        foreach ($config as $element) {
            $validator = $element['validator'];
            if (!Mage::getStoreConfig("collpur/{$element['alias']}/enabled") && $this->{$validator}()) {
                return false;
            }
        }
        return true;
    }

    public function closeAsSuccess() {
        if (!$this->getData('is_success')) {
            throw new Mage_Core_Exception(Mage::helper('collpur')->__('This deal can not be closed as success'));
        }
        if ($this->getData('enable_coupons') && $this->getPurchasesCount() > Mage::getModel('collpur/coupon')->getCouponCountForDeal($this)) {
            throw new Mage_Core_Exception(Mage::helper('collpur')->__('Not enough coupons to close this deal'));
        }

        $this->close();

        $this->additionalProductProcessing();
    }

    public function closeAsFailed() {
        $errors = array();
        foreach ($this->getPurchasesCollection() as $purchase) {
            try {
                $purchase->refund();
            } catch (Exception $ex) {
                $order = Mage::getModel('sales/order')->load($purchase->getData('order_id'));
                $errors[] = Mage::helper('collpur')->__('Order %s: %s', $order->getIncrementId(), $ex->getMessage());
            }
        }

        /*
         *  !Only for order tags 1.1 compatibility
         *  Close as failed means automatic refund process,
         *  it means order re-save.
         *  Order tags catch such events as well as collpur integration.
         *  To avoid revalidation of the same orders set skip flag to Mage::registry
         *  will be use in AW_Ordertags_Model_Observer->orderStatusChanged      
         */
        Mage::register('aw_collpur_close_as_failed', 1, true);
        /* */

        $this->close();
        if ($errors)
            throw new Exception(implode('<br />', $errors));
        Mage::getModel('collpur/coupon')->getCollection()->setAsExpired($this->getId());
    }

    public function close() {
        $this->setData('close_state', self::STATE_CLOSED)->save();
        // For integration with Order Tags 1.1 version
        Mage::dispatchEvent('aw_collpur_deal_status_changed', array('deal' => $this));
    }

    public function archive() {
        $this->setData('close_state', self::STATE_ARCHIVED)->save();
    }

    public function getPurchasesCollection() {
        return
                Mage::getModel('collpur/dealpurchases')
                ->getCollection()
                ->addFieldToFilter('deal_id', $this->getId());
    }

    public function loadActiveDealByProduct($productId) {
        $this->getResource()->loadActiveDealByProduct($this, $productId);
        return $this;
    }

    protected function _loadOrderItemIds() {
        return $this->_getResource()->loadOrderItemIds($this->getId());
    }

}