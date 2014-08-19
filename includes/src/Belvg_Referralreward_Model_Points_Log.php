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

class Belvg_Referralreward_Model_Points_Log extends Mage_Core_Model_Abstract
{
    /**
     * Statuses of subscriber
     * 0 - subscriber has blocked, if point were spent
     * 1 - customer was subscribed
     */
    const STATUS_NEWSLETTER_BLOCKED    = 0;
    const STATUS_NEWSLETTER_SUBSCRIBED = 1;

    /**
     * Statuses of review
     * 0 - review has blocked, if point were spent
     * 1 - customer review was approved
     */
    const STATUS_REVIEW_BLOCKED  = 0;
    const STATUS_REVIEW_APPROVED = 1;

    /**
     * types
     */
    const TYPE_ADMIN                 = 'admin';
    const TYPE_NEWSLETTER            = 'newsletter';
    const TYPE_REVIEW                = 'review';
    const TYPE_SALES                 = 'sales';
    const TYPE_CUSTOMER_REGISTRATION = 'registration';
    const TYPE_CUSTOMER_INVITER      = 'inviter';
    const TYPE_CUSTOMER_INVITEE      = 'invitee';
    const TYPE_SOCIAL_SHARE_PRODUCT  = 'share_product';
    const TYPE_SOCIAL_SHARE_PAGE     = 'share_page';
    const TYPE_TRANSFER              = 'transfer';

    public function _construct()
    {
        parent::_construct();
        $this->_init('referralreward/points_log');
    }

    /**
     * Get 'Point Log' object, filtered by params:
     *
     * @param int Customer id
     * @param int Object id
     * @param int Type id
     * @param int Status
     * @return Mage_Referralreward_Model_Points_Log
     */
    public function getItem($customerId, $objectId = FALSE, $type = FALSE, $status = FALSE)
    {
        $collection = $this->getCollection()->addFieldToFilter('customer_id', $customerId);

        if ($objectId && $type) {
            $collection->addFieldToFilter('object_id', $objectId);
            $collection->addFieldToFilter('type', $type);
        }

        if ($status) {
            $collection->addFieldToFilter('status', $status);
        }

        return $collection->getFirstItem();
    }

    public function withdrawPoints($customerId, $points = 0)
    {
        $points = (int) $points;
        if ($points == 0) {
            return;
        }

        $helper      = Mage::helper('referralreward');
        $currentTime = $helper->getTime(TRUE);
        $collection  = $this->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('points', array('gt' => 0))
            ->addFieldToFilter('end_at', array('gt' => $currentTime));
        $collection->getSelect()->order('end_at asc');

        foreach ($collection AS $item) {
            $itemPoints      = $item->getPoints();
            if ($points <= $itemPoints) {
                $itemPoints -= $points;
                $points      = 0;
                $item
                    ->setPoints($itemPoints)
                    ->setUpdatedAt($helper->getTime())
                    ->save();

                break;
            } else {
                $item
                    ->setPoints(0)
                    ->setUpdatedAt($helper->getTime())
                    ->save();
                $points -= $itemPoints; // for next item
            }
        }

        Mage::getModel('referralreward/points')->calculate($customerId);
    }

    public function supplementPoints($customerId, $params)
    {
        $helper      = Mage::helper('referralreward');
        $currentTime = $helper->getTime();
        $params['created_at'] = date('Y-m-d H:i:s', $currentTime);
        $params['updated_at'] = date('Y-m-d H:i:s', $currentTime);
        $params['end_at']     = date('Y-m-d H:i:s', $helper->getEndTime($currentTime));
        //print_r($params); die;

        $this->setData($params)->save();
        Mage::getModel('referralreward/points')->calculate($customerId);

        if ($params['points']) {
            $this->sendToEmail($customerId, $params['points']);
        }

        return $this;
    }

    /**
     * Send email
     *
     * @param string Recepient email
     * @param string Recepient first, last name
     * @param string Referral link current customer
     * @param string Adding a message to the email template
     * @return Mage_Referralreward_Model_Friends_Collection
     */
    public function sendToEmail($customerId, $points)
    {
        $helper           = Mage::helper('referralreward');
        $templateConfigId = $helper->storeConfig('settings/email_template_notification');
        $storeId          = Mage::app()->getStore();

        $customer         = Mage::getModel('customer/customer')->load($customerId);
        if ($customerId && $points) {
            $senderName     = Mage::getStoreConfig('trans_email/ident_support/name', $storeId);
            $senderEmail    = Mage::getStoreConfig('trans_email/ident_support/email', $storeId);
            $recepientName  = $customer->getFirstname() . ' ' . $customer->getLastname();
            $recepientEmail = $customer->getEmail();
            $message        = '';

            //Set variables that can be used in email template
            $vars = array(
                'points'  => $points,
                'message' => $message,
            );

            $helper->sendEmail($templateConfigId, $senderEmail, $senderName, $recepientEmail, $recepientName, $vars);
        }
    }

    public function checkPointsEndAt()
    {
        $helper = Mage::helper('referralreward');
        if ($helper->storeConfig('settings/point_lifetime_enabled')) {
            $customerId  = (int) Mage::getSingleton('customer/session')->getId();
            $currentTime = $helper->getTime(TRUE);
            $collection  = $this->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('points', array('gt' => 0))
                ->addFieldToFilter('end_at', array('lt' => $currentTime));

            if ($collection->count()) {
                $collection->clearPoints($customerId, $currentTime);
                Mage::getModel('referralreward/points')->calculate($customerId);
            }
        }
    }
}