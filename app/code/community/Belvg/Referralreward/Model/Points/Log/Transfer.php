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

class Belvg_Referralreward_Model_Points_Log_Transfer extends Belvg_Referralreward_Model_Points_Log_Abstract
{
    /**
     * Supplement Points
     *
     * @param Mage_Customer_Model_Customer
     * @param array
     * @return Belvg_Referralreward_Model_Points_Log | boolean
     */
    public function supplementPoints($recipient, $params = array())
    {
        $helper = Mage::helper('referralreward');
        $points = (int) $helper->storeConfig('points/registration');
        if ($points && isset($params['object_id']) && isset($params['points'])) {
            $params['type']        = Belvg_Referralreward_Model_Points_Log::TYPE_TRANSFER;
            $params['points_orig'] = $params['points'];
            $params['customer_id'] = $recipient->getId();

            return Mage::getModel('referralreward/points_log')
                ->supplementPoints($recipient->getId(), $params);
        }

        return FALSE;
    }

    /**
     * Withdraw Points
     *
     * @param Varien_Object
     */
    public function withdrawPoints($object)
    {
        Mage::getModel('referralreward/points_log')->withdrawPoints($object->getCustomerId(), $object->getWithdrawPoints());
    }

    /**
     * Get Log Item Title
     *
     * @return string
     */
    public function getLogTitle()
    {
        return Mage::helper('referralreward')->__('Transfer');
    }

    /**
     * Get Log Item Description
     *
     * @param int Current Item Object Id
     * @return string
     */
    public function getLogDescription()
    {
        if ($this->getEmail()) {
            return Mage::helper('referralreward')->__('From: %s<p>%s</p>', $this->getFirstname() . ' ' . $this->getLastname(), $this->getEmail());
        } else {
            return Mage::helper('referralreward')->__('Customer') . ': ' . $this->getId();
        }
    }

    /**
     * load Loag Item Object
     *
     * @param int Current Item Object Id
     * @return string
     */
    public function load($objectId)
    {
        $customer = Mage::getModel('customer/customer')->load($objectId);
        if ($customer->getId()) {
            $this->setData($customer->getData());
        } else {
            $this->setId($objectId);
        }

        return $this;
    }
}
