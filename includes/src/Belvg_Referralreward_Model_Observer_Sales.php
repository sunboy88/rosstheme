<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
/******************************************
 *      MAGENTO EDITION USAGE NOTICE      *
 ******************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/******************************************
 *      DISCLAIMER                        *
 ******************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 ******************************************
 * @category   Belvg
 * @package    Belvg_Referralreward
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Referralreward_Model_Observer_Sales
{
    protected function _supplementPoints($order)
    {
        $helper     = Mage::helper('referralreward');
        $orderAmt   = (float) $order->getGrandTotal();
        $minOrder   = (float) str_replace(',', '.', $helper->storeConfig('points/minorder'));
        if ($orderAmt >= $minOrder) {
            if ($helper->storeConfig('points/to_all')) {
                $friends = Mage::getModel('referralreward/friends')->loadCurrentSuccessFriends($order->getCustomerId());
            } else {
                // Belvg_Referralreward_Model_Friends::FRIEND_BRING ( == 2 )
                $friends = array(0 => Mage::getModel('referralreward/friends')->loadCurrentSuccessFriend($order->getCustomerId()));
            }

            foreach ($friends AS $friend) {
                if ($friend->getId()) {
                    // Inviter added points
                    $helper->getLogModel(Belvg_Referralreward_Model_Points_Log::TYPE_CUSTOMER_INVITER)->supplementPoints($order, array('customer_id' => $friend->getCustomerId()));
                    // Friend change status
                    $friend->setStatus( Belvg_Referralreward_Model_Friends::FRIEND_INVITED_ACTIVE )->save();

                    $this->_moveCustomerToGroup($friend->getCustomerId());
                }
            }

            if (count($friends)) {
                // Invitee added points
                $helper->getLogModel(Belvg_Referralreward_Model_Points_Log::TYPE_CUSTOMER_INVITEE)->supplementPoints($order);
            }
        }

        // Check added products points
        $helper->getLogModel(Belvg_Referralreward_Model_Points_Log::TYPE_SALES)->supplementPoints($order);
    }

    protected function _withdrawPoints($order)
    {
        $helper = Mage::helper('referralreward');
        $helper->getLogModel(Belvg_Referralreward_Model_Points_Log::TYPE_CUSTOMER_INVITER)->withdrawPoints($order);
        $helper->getLogModel(Belvg_Referralreward_Model_Points_Log::TYPE_CUSTOMER_INVITEE)->withdrawPoints($order);
        $helper->getLogModel(Belvg_Referralreward_Model_Points_Log::TYPE_SALES)->withdrawPoints($order);
    }

    /**
     *  <events>
     *      <checkout_onepage_controller_success_action>
     *      <checkout_multishipping_controller_success_action>
     */
    public function checkoutSuccess(Varien_Event_Observer $observer)
    {
        $helper     = Mage::helper('referralreward');
        $customerId = (int) Mage::getSingleton('customer/session')->getId();
        if ($helper->isEnabled() && $customerId) {
            $settings = $helper->getSettings();
            if (Mage::getSingleton('core/session')->getPoints() == 'used') {
                $orderId = (int) Mage::getSingleton('checkout/session')->getLastOrderId();
                $order   = Mage::getModel('sales/order')->load($orderId);
                if ($settings['use_coupon']) {
                    $discount = (float) $order->getDiscountAmount();
                } else {
                    $discount = (float) $order->getReferralrewardAmount();
                }

                $discountPoints = $helper->convertAmount($discount);
                Mage::getModel('referralreward/points_log')->withdrawPoints($customerId, $discountPoints);
            }

            Mage::getSingleton('core/session')->unsPointsDiscount();
            Mage::getSingleton('core/session')->unsMycredit();
        }
    }

    public function orderSaveAfter(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('referralreward');
        $order  = $observer->getEvent()->getOrder();
        if ($order->getData('state') == $order->getOrigData('state') || !$helper->isEnabled()) {
            return $this;
        }

        if ($order->getState() == $helper->storeConfig('points/invitation_order_status')) {
            $this->_supplementPoints($order);
        } else if (($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED || $order->getState() == Mage_Sales_Model_Order::STATE_CLOSED) && $helper->storeConfig('points/order_cancel')) {
            $this->_withdrawPoints($order);
        }

        return $this;
    }

    protected function _moveCustomerToGroup($friendCustomerId)
    {
        $helper = Mage::helper('referralreward');
        if ($helper->storeConfig('move_group/enabled')) {
            $moveGroups = $helper->decodeStoreConfigMoveGroupTo();
            $customer   = Mage::getModel('customer/customer')->load($friendCustomerId);
            if ($customer->getId()) {
                $invited = (int) Mage::getModel('referralreward/friends')
                    ->getFriendsCollection($friendCustomerId, Belvg_Referralreward_Model_Friends::FRIEND_INVITED_ACTIVE)
                    ->count();

                if ($invited) {
                    $group = 0;
                    foreach ($moveGroups AS $moveGroup) {
                        if ($moveGroup['invited'] == $invited) {
                            $group = $moveGroup['group'];
                            break;
                        }
                    }

                    if ($group) {
                        $customer->setGroupId($group)->save();
                    }
                }
            }
        }
    }
}
 
 