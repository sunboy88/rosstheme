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

class Belvg_Referralreward_Model_Points_Log_Newsletter extends Belvg_Referralreward_Model_Points_Log_Abstract
{
    /**
     * Supplement Points
     *
     * @param Mage_Newsletter_Model_Subscriber
     * @param array
     * @return Belvg_Referralreward_Model_Points_Log | boolean
     */
    public function supplementPoints($subscriber, $params = array())
    {
        $helper = Mage::helper('referralreward');
        $points = (int) $helper->storeConfig('points/newsletter');
        if ($points) {
            $params['status']      = Belvg_Referralreward_Model_Points_Log::STATUS_NEWSLETTER_SUBSCRIBED;
            $params['type']        = Belvg_Referralreward_Model_Points_Log::TYPE_NEWSLETTER;
            $params['object_id']   = $subscriber->getId();
            $params['points']      = $points;
            $params['points_orig'] = $points;
            $params['customer_id'] = $subscriber->getCustomerId();

            return Mage::getModel('referralreward/points_log')
                ->supplementPoints($subscriber->getCustomerId(), $params);
        }

        return FALSE;
    }

    /**
     * Withdraw Points
     *
     * @param Belvg_Referralreward_Model_Points_Log
     */
    public function withdrawPoints($newsletterLog)
    {
        if ($newsletterLog->getPoints() < $newsletterLog->getPointsOrig()) {
            $pointsItem = Mage::getModel('referralreward/points')->getItem($newsletterLog->getCustomerId());
            if ($pointsItem->getPoints() >= $newsletterLog->getPointsOrig()) {
                $pointsReturn = $newsletterLog->getPointsOrig() - $newsletterLog->getPoints();
                $newsletterLog->delete();
                $newsletterLog->withdrawPoints($newsletterLog->getCustomerId(), $pointsReturn);
            } else {
                $newsletterLog->setStatus(Belvg_Referralreward_Model_Points_Log::STATUS_NEWSLETTER_BLOCKED)->save();
                $newsletterLog->withdrawPoints($newsletterLog->getCustomerId(), $pointsItem->getPoints());
            }
        } else {
            $newsletterLog->delete();
            Mage::getModel('referralreward/points')->calculate($newsletterLog->getCustomerId());
        }
    }

    /**
     * Get Log Item Title
     *
     * @return string
     */
    public function getLogTitle()
    {
        return Mage::helper('referralreward')->__('Subscription');
    }

    /**
     * Get Log Item Description
     *
     * @param int Current Item Object Id
     * @return string
     */
    public function getLogDescription()
    {
        return '';
    }
}
