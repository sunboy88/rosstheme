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

class Belvg_Referralreward_Model_Points_Log_Registration extends Belvg_Referralreward_Model_Points_Log_Abstract
{
    /**
     * Supplement Points
     *
     * @param Mage_Customer_Model_Customer
     * @param array
     * @return Belvg_Referralreward_Model_Points_Log | boolean
     */
    public function supplementPoints($customer, $params = array())
    {
        $helper = Mage::helper('referralreward');
        $points = (int) $helper->storeConfig('points/registration');
        if ($points) {
            $params['type']        = Belvg_Referralreward_Model_Points_Log::TYPE_CUSTOMER_REGISTRATION;
            $params['object_id']   = $customer->getId();
            $params['points']      = $points;
            $params['points_orig'] = $points;
            $params['customer_id'] = $customer->getId();

            return Mage::getModel('referralreward/points_log')
                ->supplementPoints($customer->getId(), $params);
        }

        return FALSE;
    }

    /**
     * Withdraw Points
     *
     * @param object
     */
    public function withdrawPoints($object)
    {
        return;
    }

    /**
     * Get Log Item Title
     *
     * @return string
     */
    public function getLogTitle()
    {
        return Mage::helper('referralreward')->__('Registration');
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
