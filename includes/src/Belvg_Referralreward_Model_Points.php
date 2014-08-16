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

class Belvg_Referralreward_Model_Points extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('referralreward/points');
    }

    public function calculate($customerId)
    {
        $pointsItem = $this->getItem($customerId);
        $points     = (int) Mage::getModel('referralreward/points_log')->getCollection()->getPointsSumm($customerId);
        if ($points != $pointsItem->getPoints()) {
            $pointsItem->setPoints($points)->save();
        }

        if ($points == 0) {
            $coupon = Mage::getModel('salesrule/coupon')->load($pointsItem->getCouponCode(), 'code');
            $rule   = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());
            if ($rule->getId()) {
                $rule->delete();
            }
        }
        
        return $points;
    }

    /**
     * Get 'Point' object, filtered by customer
     *
     * @param int Customer id
     * @return Mage_Referralreward_Model_Points
     */
    public function getItem($customer_id)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('customer_id', array('gt' => 0));

        return $collection->getFirstItem();
    }

    /**
     * Get 'Point' object, filtered by referral link
     *
     * @param string Referral link
     * @return Mage_Referralreward_Model_Points
     */
    public function getItemByUrl($url)
    {
        $collection = $this->getCollection()->addFieldToFilter('url', $url);

        return $collection->getFirstItem();
    }

    /**
     * Saving customer's referral link
     *
     * @param int Customer id
     * @param string Referral link
     */
    public function saveInviteLink($customer_id, $renamelink)
    {
        $item = $this->getItem($customer_id);
        $item->setUrl($renamelink)->save();
    }
}