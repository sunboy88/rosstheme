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

class Belvg_Referralreward_Block_Account_Log extends Mage_Core_Block_Template
{
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function getItems()
    {
        $customerId = (int) $this->_getSession()->getId();
        $collection = Mage::getModel('referralreward/points_log')->getCollection()
            ->addFieldToFilter('customer_id', $customerId);
        $collection->getSelect()->order('id desc');

        return $collection;
    }

    /**
     * Get item row html
     *
     * @param   Belvg_Referralreward_Model_Points_Log $item
     * @return  string
     */
    public function getItemHtml(Belvg_Referralreward_Model_Points_Log $item)
    {
        $block = $this->getLayout()->getBlock('log-list-item');
        $block->setItem($item);
        if (! $block instanceof Mage_Core_Block_Template) {
            return '';
        }

        return $block->toHtml();
    }
}