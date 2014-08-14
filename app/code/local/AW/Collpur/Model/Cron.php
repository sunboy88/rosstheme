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

class AW_Collpur_Model_Cron
{
    public static function checkAndSendEmails() {

        self::_processExpiredDeals();
        self::_processExpireAfterDaysDeals();
        self::_processSuccessedDeals();

    }

    public static function _processExpireAfterDaysDeals() {
 
        if (!$days = (int) Mage::getStoreConfig('collpur/notifications/notify_admin_before_deal_expired')) return; 
        $expiredDeals = Mage::getModel('collpur/deal')->getCollection()->getActiveDealsForCron()->addEnabledFilter()->getExpiredAfterDaysFilter($days);
 
        $data = array();
        $data['templates'] = array('admin' => array('notifyAdminBeforeDealExpired'),'customer' => array());
        $data['expire_after_days'] = $days;
        $data['flag'] = 'getSentBeforeFlag';
        self::_processPurchases($expiredDeals, $data); 
        $expiredDeals->setExpiredFlag($expiredDeals, 'setSentBeforeFlag');

    }

    public static function _processExpiredDeals() {

        $expiredDeals = Mage::getModel('collpur/deal')->getCollection()->getExpiredFilter()->addNotProcessedFlag()->addEnabledFilter();
        $data = array();
        $data['templates'] = array('admin' => array('dealExpiredTemplateAdmin'),'customer' => array('dealExpiredTemplateCustomer'));
        $data['flag'] = 'getExpiredFlag';
        self::_processPurchases($expiredDeals, $data);        
        $expiredDeals->setExpiredFlag($expiredDeals);
        
    }

    public static function _processSuccessedDeals() {

        $successedDeals = Mage::getModel('collpur/deal')->getCollection()->getSuccessedDeals()->addEnabledFilter();
 
        $data = array();
        $data['templates'] = array('admin' => array('dealSucceedTemplateAdmin'),'customer' => array('dealSucceedTemplateCustomer'));
        $data['flag'] = 'getIsSuccessedFlag';
        self::_processPurchases($successedDeals, $data);
        $successedDeals->setExpiredFlag($successedDeals,'setIsSuccessedFlag');

    }

     private static function _processPurchases($collection, $data=array()) {

        if ($collection->count() == 0) { return; }

        $dealModel = Mage::getModel('collpur/deal');
        $orderItem = Mage::getModel('sales/order_item');

        /* Flag needed for admin sending */
        $flag = $data['flag'];
        /* send depending on specific flag i.e mode */

        foreach ($collection as $deal) {

            /* Process admin sending  Notifications for admin have global scope */
            if (Mage::getStoreConfig(AW_Collpur_Helper_Notifications::NOTIFICATIONS_ON) && !$deal->{$flag}()) {
                if (!empty($data['templates']['admin'])) {
                    $deal->setEditPage(Mage::getUrl('collpur_admin/adminhtml_deal/edit', array('id' => $deal->getId())));
                    foreach ($data['templates']['admin'] as $key => $template) {
                        Mage::helper('collpur/notifications')->processEmails($template, array('deal' => $deal, 'params' => $data));
                    }
                }
            }

            /* Process customer sending
             * is_successed_flag 0 means should be processed
             * is_successed_flag 2 means should not be processed because at the moment of purchase notifications
             * for the store were disabled
              */
            
            if (!empty($data['templates']['customer'])) {
                $dealPurchases = Mage::getModel('collpur/dealpurchases')->getCollection()->addFieldToFilter('deal_id', array('eq' => $deal->getId()))
                        ->addFieldToFilter('is_successed_flag',array('eq'=> 0))
                        ->addFieldToFilter('is_successed_flag',array('neq'=>2));


                foreach ($dealPurchases as $purchase) {
                    
                    $order = Mage::getModel('sales/order_item')->load($purchase->getOrderItemId())->getOrder();
                    /* Continue with iteration if order is deleted */
                    if(!$order) { continue; } 
                    /* Process email template only if notifications are enabled for the store. Note: store is taken from order */
                    if(!Mage::getStoreConfig(AW_Collpur_Helper_Notifications::NOTIFICATIONS_ON,$order->getStoreId())) { continue; } 
                 
                   $order->setAccountLink(Mage::getUrl('customer/account', array('_secure' => Mage::app()->getStore(true)->isCurrentlySecure(), '_store' => $order->getStoreId())));

                  $c = 0; foreach ($data['templates']['customer'] as $key => $template) {
                        /* IF we process expired template, send email at once as it doesn't matter is there is deal success or not */
                        if ($template == 'dealExpiredTemplateCustomer') {
                            Mage::helper('collpur/notifications')->processEmails($template, array('deal' => $deal, 'order' => $order, 'params' => $data));
                            continue;
                        } elseif ($template == 'dealSucceedTemplateCustomer') {
                            /* IF coupons are disabled, we should send emails only to customer who paid for the deals */
                            if (!$deal->getEnableCoupons() && $purchase->getQtyPurchased() > 0) {
                                Mage::helper('collpur/notifications')->processEmails($template, array('deal' => $deal, 'order' => $order, 'params' => $data));
                                $purchase->setIsSuccessedFlag(1)->save();
                                continue;
                            }
                            /* IF coupons are enabled we should send emails only to customers who paid for the deal and only if there are enough coupons */
                            else if ($deal->getEnableCoupons() && $purchase->getQtyPurchased() > 0 && ($purchase->getQtyPurchased() == $purchase->getQtyWithCoupons())) {
                                   /* Generate coupons only once */
                                   if ($c == 0) {
                                        $order->setCouponCode($dealModel->collectCouponCodes($purchase->getId()));
                                        $order->setCouponExpireDate($dealModel->calculateCouponExpireDate($purchase->getId(), $deal, $order->getStoreId(),true,true));
                                        $order->setCouponPrintPage(Mage::getUrl('deals/customer/view',array('_secure' => Mage::app()->getStore(true)->isCurrentlySecure(), 'purchase_id'=>$purchase->getId(),'_store'=>$order->getStoreId())));
                                   }
                                Mage::helper('collpur/notifications')->processEmails($template, array('deal' => $deal, 'order' => $order, 'params' => $data));
                                $purchase->setIsSuccessedFlag(1)->save();
                                continue;
                            } else {
                                continue;
                            }
                        }
                        $c++;
                    }
                }
            }
        }
    }


}
