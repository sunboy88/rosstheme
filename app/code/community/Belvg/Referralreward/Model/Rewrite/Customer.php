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

class Belvg_Referralreward_Model_Rewrite_Customer extends Mage_Customer_Model_Customer
{
    protected function _supplementRegistrationPoints($customerId)
    {
        $helper = Mage::helper('referralreward');
        $points = (int) $helper->storeConfig('points/registration');
        if ($points) {
            $params = array(
                'status'      => 0,
                'type'        => Belvg_Referralreward_Model_Points_Log::TYPE_CUSTOMER_REGISTRATION,
                'object_id'   => $customerId,
                'points'      => $points,
                'points_orig' => $points,
                'customer_id' => $customerId,
            );

            Mage::getModel('referralreward/points_log')->supplementPoints($customerId, $params);
        }
    }

    /**
     *  Verifying the referral link registration
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0')
    {
        $customer = $this;
        $helper   = Mage::helper('referralreward');
        if ($helper->isEnabled() && ($type == 'registered' || $type == 'confirmed')) {
            $invitelink  = Mage::app()->getRequest()->getParam('friend_invitelink');
            $sessionMark = Mage::getSingleton('customer/session')->getReferralInvitedCustomer();
            if ($invitelink || $sessionMark) {
                if ($invitelink) {
                    $pointsItem = Mage::getModel('referralreward/points')->getItemByUrl($invitelink);
                } else {
                    $pointsItem = Mage::getModel('referralreward/points')->getItemByUrl($sessionMark);
                }

                $friend = Mage::getModel('referralreward/friends')->getItem($pointsItem->getCustomerId(), $customer->getEmail());
                if ($friend->getId()) {
                    $friend->setStatus(Belvg_Referralreward_Model_Friends::FRIEND_BRING)->save();
                } else {
                    $data                 = array();
                    $data['friend_name']  = $customer->getFirstname() . ' ' . $customer->getLastname();
                    $data['friend_email'] = $customer->getEmail();
                    $data['customer_id']  = $pointsItem->getCustomerId();
                    $data['status']       = Belvg_Referralreward_Model_Friends::FRIEND_BRING;
                    $friend->setData($data)->save();
                }
 
                $collection = Mage::getModel('referralreward/friends')->getOtherItems($pointsItem->getCustomerId(), $customer->getEmail());
                $collection->setDataToAll('status', Belvg_Referralreward_Model_Friends::FRIEND_NO_BRING)->save();
            }

            $helper->createNewPointsObject($customer->getId());
            $helper->getLogModel(Belvg_Referralreward_Model_Points_Log::TYPE_CUSTOMER_REGISTRATION)->supplementPoints($customer);
        }

        return parent::sendNewAccountEmail($type, $backUrl, $storeId);
    }

}
