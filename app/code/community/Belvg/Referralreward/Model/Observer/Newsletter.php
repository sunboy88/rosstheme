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

class Belvg_Referralreward_Model_Observer_Newsletter
{
    public function subscriberSaveAfter(Varien_Event_Observer $observer)
    {
        $helper           = Mage::helper('referralreward');
        $pointsNewsletter = $helper->storeConfig('points/newsletter');
        if (!$helper->isEnabled() || !$pointsNewsletter) {
            return;
        }

        $subscriber = $observer->getEvent()->getSubscriber();
        if ($subscriber->getCustomerId() && $subscriber->getIsStatusChanged()) {
            $newsletterLog = Mage::getModel('referralreward/points_log')->getItem(
                $subscriber->getCustomerId(),
                $subscriber->getId(),
                Belvg_Referralreward_Model_Points_Log::TYPE_NEWSLETTER
            );

            if (!$newsletterLog->getId() && $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
                $helper->getLogModel(Belvg_Referralreward_Model_Points_Log::TYPE_NEWSLETTER)->supplementPoints($subscriber);
            } else if ($newsletterLog->getId() && $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED && $helper->storeConfig('points/newsletter_remove_points')) {
                if ($newsletterLog->getStatus() != Belvg_Referralreward_Model_Points_Log::STATUS_NEWSLETTER_BLOCKED) {
                    $helper->getLogModel(Belvg_Referralreward_Model_Points_Log::TYPE_NEWSLETTER)->withdrawPoints($newsletterLog);
                }
            }
        }
    }
}
 
 